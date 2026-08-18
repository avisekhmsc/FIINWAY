<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'address_id' => 1,
            'order_number' => 'ORD-' . fake()->numerify('#####'),
            'subtotal' => 1000,
            'delivery_charge' => 0,
            'discount' => 0,
            'total' => 1000,
            'status' => 'pending',
            'payment_status' => 'paid',
        ];
    }
}
