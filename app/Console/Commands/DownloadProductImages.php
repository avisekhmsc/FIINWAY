<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadProductImages extends Command
{
    protected $signature = 'products:download-images';
    protected $description = 'Download and validate product images from legitimate sources into local storage';

    protected array $demoImages = [
        'sony-wh-1000xm5' => [
            'main' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=1200&auto=format&fit=crop',
            'gallery2' => 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?q=80&w=1200&auto=format&fit=crop',
            'gallery3' => 'https://images.unsplash.com/photo-1484704849700-f032a568e944?q=80&w=1200&auto=format&fit=crop',
        ],
        'samsung-galaxy-s24' => [
            'main' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?q=80&w=1200&auto=format&fit=crop',
            'gallery2' => 'https://images.unsplash.com/photo-1580910051074-3eb694886505?q=80&w=1200&auto=format&fit=crop',
            'gallery3' => 'https://images.unsplash.com/photo-1565849904461-04a58ad377e0?q=80&w=1200&auto=format&fit=crop',
        ],
        'oneplus-12r' => [
            'main' => 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?q=80&w=1200&auto=format&fit=crop',
            'gallery2' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?q=80&w=1200&auto=format&fit=crop',
        ],
        'macbook-pro-m3' => [
            'main' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=1200&auto=format&fit=crop',
            'gallery2' => 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?q=80&w=1200&auto=format&fit=crop',
            'gallery3' => 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?q=80&w=1200&auto=format&fit=crop',
        ],
        'iphone-13-pro-max' => [
            'main' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?q=80&w=1200&auto=format&fit=crop',
            'gallery2' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?q=80&w=1200&auto=format&fit=crop',
            'gallery3' => 'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?q=80&w=1200&auto=format&fit=crop',
        ],
    ];

    public function handle(): int
    {
        $this->info('Starting Product Image Sourcing & Downloading...');

        // Ensure storage directory exists
        Storage::disk('public')->makeDirectory('products');

        // Create default fallback image if not present
        $this->ensureDefaultFallbackImage();

        $processedProducts = 0;
        $downloadedImages = 0;
        $failedImages = 0;

        foreach ($this->demoImages as $slug => $urls) {
            $product = Product::where('slug', $slug)->first();
            if (!$product) {
                // Try matching by name
                $product = Product::all()->first(fn($p) => Str::slug($p->name) === $slug);
            }

            if (!$product) {
                $this->warn("Product not found in database for slug: {$slug}");
                continue;
            }

            $this->info("\nDownloading {$product->name}...");
            $productDir = "products/{$slug}";
            Storage::disk('public')->makeDirectory($productDir);

            // Clear old image records for clean rebuild
            ProductImage::where('product_id', $product->id)->delete();

            $sortOrder = 0;
            foreach ($urls as $key => $url) {
                try {
                    $response = Http::timeout(20)->get($url);

                    if (!$response->successful()) {
                        $this->error("  ✗ Failed HTTP status for {$key}: {$response->status()}");
                        $failedImages++;
                        continue;
                    }

                    $contentType = $response->header('Content-Type');
                    if (!str_contains($contentType, 'image/')) {
                        $this->error("  ✗ Invalid Content-Type for {$key}: {$contentType}");
                        $failedImages++;
                        continue;
                    }

                    $extension = match(true) {
                        str_contains($contentType, 'webp') => 'webp',
                        str_contains($contentType, 'png') => 'png',
                        default => 'jpg',
                    };

                    $filename = $key === 'main' ? "main.{$extension}" : "{$sortOrder}.{$extension}";
                    $relativePath = "{$productDir}/{$filename}";

                    Storage::disk('public')->put($relativePath, $response->body());

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $relativePath,
                        'is_primary' => $key === 'main',
                        'sort_order' => $sortOrder,
                    ]);

                    $this->info("  ✓ {$key} image downloaded -> /storage/{$relativePath}");
                    $downloadedImages++;
                    $sortOrder++;

                } catch (\Throwable $e) {
                    $this->error("  ✗ Exception downloading {$key}: " . $e->getMessage());
                    $failedImages++;
                }
            }

            $processedProducts++;
        }

        $this->newLine();
        $this->info("=======================================");
        $this->info("Products processed: {$processedProducts}");
        $this->info("Images downloaded: {$downloadedImages}");
        $this->info("Images failed: {$failedImages}");
        $this->info("=======================================");

        return 0;
    }

    protected function ensureDefaultFallbackImage()
    {
        $defaultPath = 'products/default.webp';
        if (!Storage::disk('public')->exists($defaultPath)) {
            // Fetch clean placeholder SVG/Image from Unsplash
            try {
                $res = Http::timeout(10)->get('https://images.unsplash.com/photo-1560343090-f0409e92791a?q=80&w=800&auto=format&fit=crop');
                if ($res->successful()) {
                    Storage::disk('public')->put($defaultPath, $res->body());
                }
            } catch (\Throwable $e) {
                // Ignore fallback download fail
            }
        }
    }
}
