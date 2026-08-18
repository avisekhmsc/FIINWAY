<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\Category;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Truncate everything cleanly ─────────────────────────────────────
        DB::statement('PRAGMA foreign_keys = OFF');
        ProductImage::truncate();
        Product::truncate();
        DB::table('wishlists')->truncate();
        DB::table('cart_items')->truncate();
        DB::table('carts')->truncate();
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        DB::table('reviews')->truncate();
        DB::table('notifications')->truncate();
        User::where('role', '!=', 'admin')->delete(); // keep admin if exists
        User::truncate();
        Category::truncate();
        DB::statement('PRAGMA foreign_keys = ON');

        // ─── 1. App Settings ─────────────────────────────────────────────────
        $settings = [
            ['key' => 'commission_percent',     'value' => '10',              'group' => 'payment',  'label' => 'Company Commission (%)'],
            ['key' => 'referral_reward',         'value' => '50',              'group' => 'referral', 'label' => 'Referral Reward Amount (₹)'],
            ['key' => 'referral_eligible_action','value' => 'first_order',     'group' => 'referral', 'label' => 'Eligible Action'],
            ['key' => 'payment_hold_days',       'value' => '2',               'group' => 'payment',  'label' => 'Payment Hold Days'],
            ['key' => 'free_delivery_threshold', 'value' => '500',             'group' => 'delivery', 'label' => 'Free Delivery Above (₹)'],
            ['key' => 'standard_delivery_fee',   'value' => '49',              'group' => 'delivery', 'label' => 'Standard Delivery Charge (₹)'],
            ['key' => 'express_delivery_fee',    'value' => '99',              'group' => 'delivery', 'label' => 'Express Delivery Charge (₹)'],
            ['key' => 'app_name',                'value' => 'FIINWAY',         'group' => 'general',  'label' => 'App Name'],
            ['key' => 'app_tagline',             'value' => 'Buy & Sell Anything', 'group' => 'general', 'label' => 'App Tagline'],
            ['key' => 'contact_email',           'value' => 'support@fiinway.com', 'group' => 'general', 'label' => 'Contact Email'],
            ['key' => 'contact_phone',           'value' => '+91-9999999999',  'group' => 'general',  'label' => 'Contact Phone'],
        ];
        foreach ($settings as $s) {
            AppSetting::updateOrCreate(['key' => $s['key']], $s);
        }

        // ─── 2. Core Users ────────────────────────────────────────────────────
        $admin = User::create([
            'name'              => 'Admin',
            'phone'             => '9999999999',
            'email'             => 'admin@fiinway.com',
            'password'          => Hash::make('admin123'),
            'role'              => 'admin',
            'is_active'         => true,
            'is_seller'         => true,
            'phone_verified_at' => now(),
            'referral_code'     => 'ADMIN001',
            'city'              => 'Mumbai',
            'state'             => 'Maharashtra',
            'pincode'           => '400001',
        ]);

        $mainSeller = User::create([
            'name'              => 'Rahul Sharma',
            'phone'             => '9876543210',
            'email'             => 'rahul@fiinway.com',
            'password'          => Hash::make('password'),
            'is_seller'         => true,
            'is_active'         => true,
            'phone_verified_at' => now(),
            'referral_code'     => 'RAHUL001',
            'city'              => 'Delhi',
            'state'             => 'Delhi',
            'pincode'           => '110001',
        ]);

        User::create([
            'name'              => 'Priya Verma',
            'phone'             => '9123456789',
            'email'             => 'priya@fiinway.com',
            'password'          => Hash::make('password'),
            'is_active'         => true,
            'phone_verified_at' => now(),
            'referral_code'     => 'PRIYA001',
            'city'              => 'Bangalore',
            'state'             => 'Karnataka',
            'pincode'           => '560001',
        ]);

        // ─── 3. Real Indian Sellers ───────────────────────────────────────────
        $indianSellers = [
            ['name'=>'Amit Gupta',      'city'=>'Mumbai',    'state'=>'Maharashtra', 'pincode'=>'400002', 'email'=>'amit@fiinway.com'],
            ['name'=>'Sneha Patel',     'city'=>'Ahmedabad', 'state'=>'Gujarat',     'pincode'=>'380001', 'email'=>'sneha@fiinway.com'],
            ['name'=>'Vikram Singh',    'city'=>'Jaipur',    'state'=>'Rajasthan',   'pincode'=>'302001', 'email'=>'vikram@fiinway.com'],
            ['name'=>'Ananya Iyer',     'city'=>'Chennai',   'state'=>'Tamil Nadu',  'pincode'=>'600001', 'email'=>'ananya@fiinway.com'],
            ['name'=>'Rohan Mehta',     'city'=>'Pune',      'state'=>'Maharashtra', 'pincode'=>'411001', 'email'=>'rohan@fiinway.com'],
            ['name'=>'Kavya Nair',      'city'=>'Kochi',     'state'=>'Kerala',      'pincode'=>'682001', 'email'=>'kavya@fiinway.com'],
            ['name'=>'Arjun Yadav',     'city'=>'Lucknow',   'state'=>'Uttar Pradesh','pincode'=>'226001','email'=>'arjun@fiinway.com'],
            ['name'=>'Pooja Saxena',    'city'=>'Hyderabad', 'state'=>'Telangana',   'pincode'=>'500001', 'email'=>'pooja@fiinway.com'],
            ['name'=>'Karan Malhotra',  'city'=>'Chandigarh','state'=>'Punjab',      'pincode'=>'160001', 'email'=>'karan@fiinway.com'],
            ['name'=>'Meera Reddy',     'city'=>'Bengaluru', 'state'=>'Karnataka',   'pincode'=>'560034', 'email'=>'meera@fiinway.com'],
            ['name'=>'Suresh Kumar',    'city'=>'Kolkata',   'state'=>'West Bengal', 'pincode'=>'700001', 'email'=>'suresh@fiinway.com'],
            ['name'=>'Divya Krishnan',  'city'=>'Coimbatore','state'=>'Tamil Nadu',  'pincode'=>'641001', 'email'=>'divya@fiinway.com'],
        ];

        $sellers = collect([$mainSeller]);
        foreach ($indianSellers as $i => $s) {
            $sellers->push(User::create([
                'name'              => $s['name'],
                'phone'             => '98' . str_pad($i + 10, 8, '0', STR_PAD_LEFT),
                'email'             => $s['email'],
                'password'          => Hash::make('password'),
                'is_seller'         => true,
                'is_active'         => true,
                'phone_verified_at' => now(),
                'referral_code'     => strtoupper(Str::random(8)),
                'city'              => $s['city'],
                'state'             => $s['state'],
                'pincode'           => $s['pincode'],
            ]));
        }

        // ─── 4. Generate 80+ Regular Buyers ──────────────────────────────────
        $indianCities = [
            ['city'=>'Mumbai',    'state'=>'Maharashtra', 'pincode'=>'400001'],
            ['city'=>'Delhi',     'state'=>'Delhi',       'pincode'=>'110001'],
            ['city'=>'Bengaluru', 'state'=>'Karnataka',   'pincode'=>'560001'],
            ['city'=>'Hyderabad', 'state'=>'Telangana',   'pincode'=>'500001'],
            ['city'=>'Chennai',   'state'=>'Tamil Nadu',  'pincode'=>'600001'],
            ['city'=>'Kolkata',   'state'=>'West Bengal', 'pincode'=>'700001'],
            ['city'=>'Pune',      'state'=>'Maharashtra', 'pincode'=>'411001'],
            ['city'=>'Ahmedabad', 'state'=>'Gujarat',     'pincode'=>'380001'],
            ['city'=>'Jaipur',    'state'=>'Rajasthan',   'pincode'=>'302001'],
            ['city'=>'Surat',     'state'=>'Gujarat',     'pincode'=>'395001'],
        ];
        $indianFirstNames = ['Aarav','Vivaan','Aditya','Vihaan','Arjun','Sai','Reyan','Arnav','Ayaan','Krishna',
                             'Ananya','Diya','Saanvi','Aarohi','Meera','Riya','Nisha','Priya','Kavya','Shreya',
                             'Rahul','Amit','Ravi','Deepak','Suresh','Ramesh','Vijay','Ajay','Sanjay','Manoj'];
        $indianLastNames  = ['Sharma','Verma','Patel','Singh','Gupta','Kumar','Joshi','Mehta','Shah','Rao',
                             'Iyer','Nair','Reddy','Pillai','Mishra','Tiwari','Pandey','Sinha','Yadav','Das'];

        for ($i = 0; $i < 80; $i++) {
            $loc = $indianCities[array_rand($indianCities)];
            $first = $indianFirstNames[array_rand($indianFirstNames)];
            $last  = $indianLastNames[array_rand($indianLastNames)];
            try {
                User::create([
                    'name'              => "$first $last",
                    'phone'             => '7' . rand(100000000, 999999999),
                    'email'             => strtolower($first . $last . $i . '@gmail.com'),
                    'password'          => Hash::make('password'),
                    'is_active'         => true,
                    'phone_verified_at' => now(),
                    'referral_code'     => strtoupper(Str::random(8)),
                    'city'              => $loc['city'],
                    'state'             => $loc['state'],
                    'pincode'           => $loc['pincode'],
                ]);
            } catch (\Throwable $e) { /* skip duplicate phone */ }
        }

        // ─── 5. Categories ────────────────────────────────────────────────────
        $categoriesData = [
            ['name'=>'Electronics',   'slug'=>'electronics',    'icon'=>'📱', 'sort_order'=>1],
            ['name'=>'Smartphones',   'slug'=>'smartphones',    'icon'=>'📲', 'sort_order'=>2],
            ['name'=>'Laptops',       'slug'=>'laptops',        'icon'=>'💻', 'sort_order'=>3],
            ['name'=>'Fashion',       'slug'=>'fashion',        'icon'=>'👕', 'sort_order'=>4],
            ['name'=>'Furniture',     'slug'=>'furniture',      'icon'=>'🪑', 'sort_order'=>5],
            ['name'=>'Home & Living', 'slug'=>'home-living',    'icon'=>'🏠', 'sort_order'=>6],
            ['name'=>'Beauty',        'slug'=>'beauty',         'icon'=>'💄', 'sort_order'=>7],
            ['name'=>'Sports',        'slug'=>'sports',         'icon'=>'⚽', 'sort_order'=>8],
            ['name'=>'Vehicles',      'slug'=>'vehicles',       'icon'=>'🚗', 'sort_order'=>9],
            ['name'=>'Accessories',   'slug'=>'accessories',    'icon'=>'👜', 'sort_order'=>10],
            ['name'=>'Watches',       'slug'=>'watches',        'icon'=>'⌚', 'sort_order'=>11],
            ['name'=>'Groceries',     'slug'=>'groceries',      'icon'=>'🛒', 'sort_order'=>12],
            ['name'=>'Tablets',       'slug'=>'tablets',        'icon'=>'📟', 'sort_order'=>13],
            ['name'=>'Skin Care',     'slug'=>'skin-care',      'icon'=>'✨', 'sort_order'=>14],
        ];
        foreach ($categoriesData as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], array_merge($cat, ['is_active' => true]));
        }
        $categoryMap = Category::pluck('id', 'slug');

        // ─── 6. Fetch REAL Products from DummyJSON API ────────────────────────
        $this->command->info('Fetching real products from DummyJSON API...');
        $response = @file_get_contents('https://dummyjson.com/products?limit=194&skip=0');
        
        if (!$response) {
            $this->command->warn('Could not reach API — skipping product seeding.');
            return;
        }

        $apiData  = json_decode($response, true);
        $apiProds = $apiData['products'] ?? [];

        // Map DummyJSON categories → our category slugs
        $catMapping = [
            'smartphones'       => 'smartphones',
            'laptops'           => 'laptops',
            'tablets'           => 'tablets',
            'mobile-accessories'=> 'electronics',
            'beauty'            => 'beauty',
            'fragrances'        => 'beauty',
            'skin-care'         => 'skin-care',
            'furniture'         => 'furniture',
            'home-decoration'   => 'home-living',
            'kitchen-accessories'=> 'home-living',
            'groceries'         => 'groceries',
            'mens-shirts'       => 'fashion',
            'mens-shoes'        => 'fashion',
            'tops'              => 'fashion',
            'womens-dresses'    => 'fashion',
            'womens-shoes'      => 'fashion',
            'womens-bags'       => 'accessories',
            'womens-jewellery'  => 'accessories',
            'sunglasses'        => 'accessories',
            'mens-watches'      => 'watches',
            'womens-watches'    => 'watches',
            'sports-accessories'=> 'sports',
            'vehicle'           => 'vehicles',
            'motorcycle'        => 'vehicles',
        ];

        $inrRate = 83; // USD → INR conversion

        foreach ($apiProds as $p) {
            $seller = $sellers->random();
            $catSlug = $catMapping[$p['category']] ?? 'electronics';
            $catId   = $categoryMap[$catSlug] ?? $categoryMap->first();

            $originalPriceINR = round($p['price'] * $inrRate);
            $discountPct      = $p['discountPercentage'] ?? 0;
            $sellingPriceINR  = round($originalPriceINR * (1 - $discountPct / 100));
            $conditionType    = in_array($p['category'], ['vehicle','motorcycle']) ? 'old' : 'new';

            $slug = Str::slug($p['title']) . '-' . Str::random(5);

            // Ensure unique slug
            while (Product::where('slug', $slug)->exists()) {
                $slug = Str::slug($p['title']) . '-' . Str::random(6);
            }

            try {
                $product = Product::create([
                    'user_id'           => $seller->id,
                    'category_id'       => $catId,
                    'name'              => $p['title'],
                    'slug'              => $slug,
                    'description'       => $p['description'] . "\n\nBrand: " . ($p['brand'] ?? 'N/A') . "\nSKU: " . ($p['sku'] ?? 'N/A'),
                    'condition_type'    => $conditionType,
                    'condition_label'   => $conditionType === 'old' ? 'Good' : null,
                    'product_age_months'=> $conditionType === 'old' ? rand(3, 24) : 0,
                    'bill_available'    => true,
                    'warranty_available'=> !empty($p['warrantyInformation']),
                    'warranty_info'     => $p['warrantyInformation'] ?? null,
                    'original_price'    => $originalPriceINR,
                    'selling_price'     => $sellingPriceINR,
                    'discount_percent'  => $discountPct,
                    'stock'             => $p['stock'] ?? rand(5, 50),
                    'status'            => 'active',
                    'delivery_type'     => 'courier',
                    'delivery_days'     => rand(2, 5),
                    'city'              => $seller->city,
                    'state'             => $seller->state,
                    'pincode'           => $seller->pincode,
                    'rating'            => $p['rating'] ?? rand(35, 50) / 10,
                    'rating_count'      => rand(10, 500),
                    'view_count'        => rand(50, 5000),
                ]);

                // Insert REAL product images from the API
                $images = $p['images'] ?? [];
                if (!empty($p['thumbnail'])) {
                    array_unshift($images, $p['thumbnail']);
                    $images = array_unique($images);
                }

                foreach (array_slice($images, 0, 5) as $idx => $imageUrl) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $imageUrl,  // store the CDN URL directly
                        'sort_order' => $idx,
                        'is_primary' => $idx === 0,
                    ]);
                }
            } catch (\Throwable $e) {
                $this->command->warn("Skipped product '{$p['title']}': " . $e->getMessage());
            }
        }

        $this->command->info('✅ Seeding complete!');
        $this->command->info('   Products: ' . Product::count());
        $this->command->info('   Users: '    . User::count());
        $this->command->info('   Images: '   . ProductImage::count());
    }
}
