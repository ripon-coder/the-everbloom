<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Return Policy',
                'slug' => 'return_policy',
                'content' => '<h1>Return Policy</h1><p>Our return policy is designed to be fair and transparent...</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Refund Policy',
                'slug' => 'refund_policy',
                'content' => '<h1>Refund Policy</h1><p>We process refunds within 7-10 business days...</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Cookie Policy',
                'slug' => 'cookie_policy',
                'content' => '<h1>Cookie Policy</h1><p>This site uses cookies to provide a better experience...</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Accessibility',
                'slug' => 'accessibility',
                'content' => '<h1>Accessibility Statement</h1><p>We are committed to ensuring digital accessibility...</p>',
                'is_active' => true,
            ],
            [
                'title' => 'About Us',
                'slug' => 'about_us',
                'content' => '<h1>About Us</h1><p>We are a leading tech retailer...</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Terms & Conditions',
                'slug' => 'terms_conditions',
                'content' => '<h1>Terms & Conditions</h1><p>Please read these terms carefully before using our services...</p>',
                'is_active' => true,
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(['slug' => $page['slug']], $page);
        }
    }
}
