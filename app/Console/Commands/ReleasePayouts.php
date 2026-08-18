<?php

namespace App\Console\Commands;

use App\Models\AppSetting;
use App\Models\Notification;
use App\Models\Payout;
use App\Models\SellerEarning;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReleasePayouts extends Command
{
    protected $signature   = 'payouts:release {--dry-run : Show what would be released without making changes}';
    protected $description = 'Release seller earnings that have passed their hold period';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $this->info($dryRun ? '[DRY RUN] Checking eligible payouts...' : 'Processing eligible payouts...');

        // Find on_hold earnings whose hold_until has passed
        $eligible = SellerEarning::with(['seller', 'order'])
            ->where('status', 'on_hold')
            ->where('hold_until', '<=', now())
            ->get();

        if ($eligible->isEmpty()) {
            $this->info('No payouts eligible for release at this time.');
            return Command::SUCCESS;
        }

        $this->info("Found {$eligible->count()} eligible payout(s).");

        $released = 0;
        $failed   = 0;

        foreach ($eligible as $earning) {
            if ($dryRun) {
                $this->line("  [DRY RUN] Would release ₹{$earning->seller_amount} for seller #{$earning->seller_id} (Order: {$earning->order->order_number})");
                continue;
            }

            DB::beginTransaction();
            try {
                // Idempotency check — never release twice
                $fresh = SellerEarning::lockForUpdate()->find($earning->id);
                if ($fresh->status !== 'on_hold') {
                    DB::rollBack();
                    $this->warn("  Skipping earning #{$earning->id}: status already changed to '{$fresh->status}'.");
                    continue;
                }

                $fresh->update([
                    'status'      => 'released',
                    'released_at' => now(),
                ]);

                // Create or update payout record
                Payout::create([
                    'seller_id'       => $fresh->seller_id,
                    'amount'          => $fresh->seller_amount,
                    'status'          => 'done',
                    'processed_at'    => now(),
                    'transaction_ref' => 'AUTO-' . strtoupper(substr(md5($fresh->id . now()), 0, 10)),
                ]);

                // Update seller wallet
                $fresh->seller->increment('wallet_balance', $fresh->seller_amount);

                // Create notification for seller
                Notification::create([
                    'user_id' => $fresh->seller_id,
                    'title'   => 'Payout Released! 🎉',
                    'body'    => "₹" . number_format($fresh->seller_amount, 2) . " from order #{$fresh->order->order_number} has been released to your wallet.",
                    'type'    => 'payout',
                    'data'    => json_encode(['earning_id' => $fresh->id, 'amount' => $fresh->seller_amount]),
                ]);

                DB::commit();

                $released++;
                $this->info("  ✓ Released ₹{$fresh->seller_amount} for seller #{$fresh->seller_id}");

            } catch (\Throwable $e) {
                DB::rollBack();
                $failed++;
                Log::error("ReleasePayouts failed for earning #{$earning->id}: " . $e->getMessage());
                $this->error("  ✗ Failed for earning #{$earning->id}: " . $e->getMessage());
            }
        }

        $this->newLine();
        if (!$dryRun) {
            $this->info("Done. Released: {$released} | Failed: {$failed}");
        }

        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
