<?php

namespace Database\Seeders;

use App\Models\Activation;
use App\Models\License;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@license.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
        ]);

        // Create regular user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // Create additional users
        $users = [
            ['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'password'],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com', 'password' => 'password'],
            ['name' => 'Bob Wilson', 'email' => 'bob@example.com', 'password' => 'password'],
        ];

        foreach ($users as $userData) {
            User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'email_verified_at' => now(),
                'password' => Hash::make($userData['password']),
            ]);
        }

        // Create multiple products
        $products = [
            [
                'name' => 'Plugin WP Pro',
                'slug' => 'plugin-wp-pro',
                'description' => 'Professional WordPress plugin with advanced features',
                'version' => '1.0.0',
                'price' => 99.00,
                'is_active' => true,
            ],
            [
                'name' => 'Theme Premium',
                'slug' => 'theme-premium',
                'description' => 'Premium WordPress theme with modern design',
                'version' => '2.1.0',
                'price' => 149.00,
                'is_active' => true,
            ],
            [
                'name' => 'E-commerce Plugin',
                'slug' => 'ecommerce-plugin',
                'description' => 'Complete e-commerce solution for WordPress',
                'version' => '3.0.0',
                'price' => 199.00,
                'is_active' => true,
            ],
            [
                'name' => 'SEO Toolkit',
                'slug' => 'seo-toolkit',
                'description' => 'Advanced SEO optimization tools',
                'version' => '1.5.0',
                'price' => 79.00,
                'is_active' => false,
            ],
            [
                'name' => 'Security Shield',
                'slug' => 'security-shield',
                'description' => 'Website security and firewall protection',
                'version' => '2.0.0',
                'price' => 129.00,
                'is_active' => true,
            ],
        ];

        $createdProducts = [];
        foreach ($products as $productData) {
            $createdProducts[] = Product::create($productData);
        }

        // Create licenses for different users and products
        $licenses = [
            // Active licenses
            [
                'user_id' => $admin->id,
                'product_id' => $createdProducts[0]->id,
                'license_key' => 'ADMIN-12345-PRO',
                'status' => 'active',
                'expires_at' => now()->addYear(),
                'max_activations' => 5,
                'notes' => 'Admin license for testing',
            ],
            [
                'user_id' => $user->id,
                'product_id' => $createdProducts[0]->id,
                'license_key' => 'TEST-67890-PRO',
                'status' => 'active',
                'expires_at' => now()->addMonths(6),
                'max_activations' => 2,
                'notes' => 'Test user license',
            ],
            [
                'user_id' => $user->id,
                'product_id' => $createdProducts[1]->id,
                'license_key' => 'THEME-11111-PREMIUM',
                'status' => 'active',
                'expires_at' => now()->addYear(),
                'max_activations' => 3,
                'notes' => 'Premium theme license',
            ],
            // Expired licenses
            [
                'user_id' => $user->id,
                'product_id' => $createdProducts[2]->id,
                'license_key' => 'ECOMM-22222-EXPIRED',
                'status' => 'expired',
                'expires_at' => now()->subMonths(2),
                'max_activations' => 1,
                'notes' => 'Expired e-commerce license',
            ],
            // Blocked licenses
            [
                'user_id' => $user->id,
                'product_id' => $createdProducts[3]->id,
                'license_key' => 'SEO-33333-BLOCKED',
                'status' => 'blocked',
                'expires_at' => now()->addYear(),
                'max_activations' => 1,
                'notes' => 'Blocked SEO license',
            ],
            // Pending licenses
            [
                'user_id' => $user->id,
                'product_id' => $createdProducts[4]->id,
                'license_key' => 'SEC-44444-PENDING',
                'status' => 'pending',
                'expires_at' => now()->addYear(),
                'max_activations' => 2,
                'notes' => 'Pending security license',
            ],
        ];

        $createdLicenses = [];
        foreach ($licenses as $licenseData) {
            $createdLicenses[] = License::create($licenseData);
        }

        // Create activations for licenses
        $activations = [
            [
                'license_id' => $createdLicenses[0]->id,
                'domain' => 'example.com',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'activated_at' => now()->subDays(30),
                'status' => 'active',
            ],
            [
                'license_id' => $createdLicenses[0]->id,
                'domain' => 'test.com',
                'ip_address' => '192.168.1.101',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
                'activated_at' => now()->subDays(15),
                'status' => 'active',
            ],
            [
                'license_id' => $createdLicenses[1]->id,
                'domain' => 'demo.com',
                'ip_address' => '192.168.1.102',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'activated_at' => now()->subDays(7),
                'status' => 'active',
            ],
            [
                'license_id' => $createdLicenses[2]->id,
                'domain' => 'website.com',
                'ip_address' => '192.168.1.103',
                'user_agent' => 'Mozilla/5.0 (Linux; Android 10; SM-G975F) AppleWebKit/537.36',
                'activated_at' => now()->subDays(5),
                'status' => 'active',
            ],
            [
                'license_id' => $createdLicenses[3]->id,
                'domain' => 'old-site.com',
                'ip_address' => '192.168.1.104',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'activated_at' => now()->subMonths(3),
                'status' => 'revoked',
            ],
        ];

        foreach ($activations as $activationData) {
            Activation::create($activationData);
        }

        $this->command->info('✅ Dummy data created successfully!');
        $this->command->info('📧 Admin login: admin@license.com / admin123');
        $this->command->info('📧 Test login: test@example.com / password');
    }
}