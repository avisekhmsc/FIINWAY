<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\Category;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // 1. App Settings
        $settings = [
            ['key' => 'commission_percent',       'value' => '10',         'group' => 'payment',  'label' => 'Company Commission (%)'],
            ['key' => 'referral_reward',            'value' => '50',         'group' => 'referral', 'label' => 'Referral Reward Amount (₹)'],
            ['key' => 'referral_eligible_action',   'value' => 'first_order','group' => 'referral', 'label' => 'Eligible Action'],
            ['key' => 'payment_hold_days',          'value' => '2',          'group' => 'payment',  'label' => 'Payment Hold Days'],
            ['key' => 'free_delivery_threshold',    'value' => '500',        'group' => 'delivery', 'label' => 'Free Delivery Above (₹)'],
            ['key' => 'standard_delivery_fee',      'value' => '49',         'group' => 'delivery', 'label' => 'Standard Delivery Charge (₹)'],
            ['key' => 'express_delivery_fee',       'value' => '99',         'group' => 'delivery', 'label' => 'Express Delivery Charge (₹)'],
            ['key' => 'app_name',                   'value' => 'FIINWAY',    'group' => 'general',  'label' => 'App Name'],
            ['key' => 'app_tagline',                'value' => 'Buy & Sell Anything', 'group' => 'general', 'label' => 'App Tagline'],
            ['key' => 'contact_email',              'value' => 'support@fiinway.com', 'group' => 'general', 'label' => 'Contact Email'],
            ['key' => 'contact_phone',              'value' => '+91-9999999999',       'group' => 'general', 'label' => 'Contact Phone'],
        ];

        foreach ($settings as $s) {
            AppSetting::updateOrCreate(['key' => $s['key']], $s);
        }

        // 2. Core Users (Admin, Demo Seller, Demo Buyer)
        User::updateOrCreate(['phone' => '9999999999'], [
            'name'              => 'Admin',
            'phone'             => '9999999999',
            'email'             => 'admin@fiinway.com',
            'password'          => Hash::make('admin123'),
            'role'              => 'admin',
            'is_active'         => true,
            'phone_verified_at' => now(),
            'referral_code'     => 'ADMIN001',
            'city'              => 'Mumbai',
            'state'             => 'Maharashtra',
            'pincode'           => '400001',
        ]);

        $seller = User::updateOrCreate(['phone' => '9876543210'], [
            'name'              => 'Rahul Sharma',
            'phone'             => '9876543210',
            'email'             => 'rahul@example.com',
            'password'          => Hash::make('password'),
            'is_seller'         => true,
            'phone_verified_at' => now(),
            'referral_code'     => 'RAHUL123',
            'city'              => 'Delhi',
            'state'             => 'Delhi',
            'pincode'           => '110001',
        ]);

        User::updateOrCreate(['phone' => '9123456789'], [
            'name'              => 'Priya Verma',
            'phone'             => '9123456789',
            'email'             => 'priya@example.com',
            'password'          => Hash::make('password'),
            'phone_verified_at' => now(),
            'referral_code'     => 'PRIYA456',
            'city'              => 'Bangalore',
            'state'             => 'Karnataka',
            'pincode'           => '560001',
        ]);

        // 3. Generate 100+ Dummy Users
        $users = [];
        for ($i = 0; $i < 100; $i++) {
            $isSeller = $faker->boolean(40); // 40% chance to be a seller
            $users[] = User::create([
                'name'              => $faker->name,
                'phone'             => '9' . $faker->numerify('#########'),
                'email'             => $faker->unique()->safeEmail,
                'password'          => Hash::make('password'),
                'is_seller'         => $isSeller,
                'phone_verified_at' => now(),
                'referral_code'     => strtoupper(Str::random(8)),
                'city'              => $faker->city,
                'state'             => $faker->state,
                'pincode'           => $faker->postcode,
                'is_active'         => $faker->boolean(95),
            ]);
        }

        // Get all sellers (including demo seller)
        $allSellers = User::where('is_seller', true)->get();

        // 4. Categories
        $categoriesData = [
            ['name' => 'Electronics',  'slug' => 'electronics',  'icon' => '📱', 'sort_order' => 1],
            ['name' => 'Fashion',      'slug' => 'fashion',      'icon' => '👕', 'sort_order' => 2],
            ['name' => 'Furniture',    'slug' => 'furniture',    'icon' => '🪑', 'sort_order' => 3],
            ['name' => 'Home & Living','slug' => 'home-living',  'icon' => '🏠', 'sort_order' => 4],
            ['name' => 'Vehicles',     'slug' => 'vehicles',     'icon' => '🚗', 'sort_order' => 5],
            ['name' => 'Computers',    'slug' => 'computers',    'icon' => '💻', 'sort_order' => 6],
            ['name' => 'Books',        'slug' => 'books',        'icon' => '📚', 'sort_order' => 7],
            ['name' => 'Sports',       'slug' => 'sports',       'icon' => '⚽', 'sort_order' => 8],
            ['name' => 'Toys',         'slug' => 'toys',         'icon' => '🧸', 'sort_order' => 9],
            ['name' => 'Beauty',       'slug' => 'beauty',       'icon' => '💄', 'sort_order' => 10],
        ];

        foreach ($categoriesData as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], array_merge($cat, ['is_active' => true]));
        }
        
        $categories = Category::all();

        // 5. Generate 120+ Dummy Products
        $productAdjectives = ['Amazing', 'Brand New', 'Classic', 'Sleek', 'Premium', 'Refurbished', 'Vintage', 'Professional', 'Lightweight', 'Heavy Duty'];
        $productNouns = ['Smartphone', 'Laptop', 'Headphones', 'Jacket', 'Sofa', 'Bike', 'Monitor', 'Novel', 'Football', 'Action Figure', 'Lipstick', 'Smartwatch', 'Camera', 'Tablet'];
        
        for ($i = 0; $i < 120; $i++) {
            $randomSeller = $allSellers->random();
            $randomCategory = $categories->random();
            $conditionType = $faker->randomElement(['new', 'old']);
            $originalPrice = $faker->numberBetween(500, 100000);
            $sellingPrice = $conditionType === 'old' ? ($originalPrice * $faker->randomFloat(2, 0.3, 0.7)) : ($originalPrice * $faker->randomFloat(2, 0.8, 1));
            
            $name = $faker->randomElement($productAdjectives) . ' ' . $faker->randomElement($productNouns) . ' ' . $faker->word;
            $slug = Str::slug($name) . '-' . Str::random(5);
            $discount = $originalPrice > 0 ? round(($originalPrice - $sellingPrice) / $originalPrice * 100, 2) : 0;
            
            Product::create([
                'user_id'          => $randomSeller->id,
                'category_id'      => $randomCategory->id,
                'name'             => $name,
                'slug'             => $slug,
                'description'      => $faker->paragraphs(3, true),
                'condition_type'   => $conditionType,
                'condition_label'  => $conditionType === 'old' ? $faker->randomElement(['Good', 'Like New', 'Fair']) : null,
                'product_age_months'=> $conditionType === 'old' ? $faker->numberBetween(1, 36) : 0,
                'bill_available'   => $faker->boolean(70),
                'warranty_available'=> $faker->boolean(40),
                'warranty_info'    => $faker->boolean(40) ? 'Valid for ' . $faker->numberBetween(1, 12) . ' months' : null,
                'original_price'   => $originalPrice,
                'selling_price'    => $sellingPrice,
                'discount_percent' => $discount,
                'stock'            => $faker->numberBetween(1, 50),
                'status'           => $faker->randomElement(['active', 'active', 'active', 'pending', 'rejected', 'inactive']),
                'delivery_type'    => 'courier',
                'delivery_days'    => $faker->numberBetween(2, 7),
                'city'             => $randomSeller->city,
                'state'            => $randomSeller->state,
                'pincode'          => $randomSeller->pincode,
                'rating'           => $faker->randomFloat(1, 3, 5),
                'rating_count'     => $faker->numberBetween(0, 500),
                'view_count'       => $faker->numberBetween(10, 10000),
            ]);
        }
    }
}
