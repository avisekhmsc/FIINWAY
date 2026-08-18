<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
class SellerEarningFactory extends Factory
{
    public function definition(): array
    {
        return [
            'seller_id' => 1,
            'order_id' => 1,
            'order_item_id' => 1,
            'order_amount' => 1000,
            'commission_percent' => 10,
            'commission_amount' => 100,
            'seller_amount' => 900,
            'status' => 'pending',
        ];
    }
}
