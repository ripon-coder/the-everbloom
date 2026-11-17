<?php

return [
    'name' => 'The Everbloom',
    'tagline' => 'Naturally Beautiful. Always Blooming.',
    'url' => 'https://the-everbloom.com',
    'currency' => [
        'code' => 'BDT',
        'symbol' => '৳',
        'position' => 'before', // before or after
        'decimals' => 0,
    ],
    'announcement' => [
        'enabled' => true,
        'message' => 'Free shipping on orders over ৳500',
        'link' => '/shop',
        'link_text' => 'Shop Now',
    ],
    // DEMO PLACEHOLDER: replace before launch
    'hero_sale_tag' => 'Up to 40% off', // DEMO PLACEHOLDER: replace before launch
    // DEMO PLACEHOLDER: replace before launch
    'shipping_and_returns_note' => 'Free standard shipping over ৳500. 30-day returns on unopened items.', // DEMO PLACEHOLDER: replace before launch
    'shipping' => [
        'free_shipping_threshold' => 500,    // DEMO PLACEHOLDER: replace before launch
        'standard_fee' => 60,                // DEMO PLACEHOLDER: replace before launch
        'express_fee' => 120,                // DEMO PLACEHOLDER: replace before launch
        'standard_rate' => 60,               // DEMO PLACEHOLDER: replace before launch
        'express_rate' => 120,               // DEMO PLACEHOLDER: replace before launch
        'standard_days' => '2–3 business days', // DEMO PLACEHOLDER: replace before launch
        'express_days' => '1–2 business days',  // DEMO PLACEHOLDER: replace before launch
    ],
    'returns' => [
        'window_days' => 30, // DEMO PLACEHOLDER: replace before launch
    ],
    'rating_breakdown' => [ // DEMO PLACEHOLDER: replace before launch
        5 => 78,
        4 => 16,
        3 => 4,
        2 => 1,
        1 => 1,
    ],
    // DEMO PLACEHOLDER: replace before launch
    'contact' => [
        'email' => 'concierge@the-everbloom.com', // DEMO PLACEHOLDER: replace before launch
        'phone' => '+880 1700-000000',            // DEMO PLACEHOLDER: replace before launch
        'hours' => 'Sat – Thu: 10:00 AM – 7:00 PM BST', // DEMO PLACEHOLDER: replace before launch
        'address' => 'House 24, Road 11, Banani, Dhaka 1213', // DEMO PLACEHOLDER: replace before launch
    ],
    // DEMO PLACEHOLDER: verify before launch
    'trust_badges' => [
        [
            'icon' => 'leaf',
            'title' => 'Carefully Selected Ingredients', // DEMO PLACEHOLDER: verify before launch
            'description' => 'Thoughtful botanical oils, hydrosols, and soothing extracts.',
        ],
        [
            'icon' => 'heart',
            'title' => 'Thoughtful Formulations', // DEMO PLACEHOLDER: verify before launch
            'description' => 'Designed for gentle, everyday skincare rituals.',
        ],
        [
            'icon' => 'globe',
            'title' => 'Amber Glass Vessels', // DEMO PLACEHOLDER: verify before launch
            'description' => 'UV-protective glass vessels to preserve freshness.',
        ],
        [
            'icon' => 'shield',
            'title' => 'Free Shipping ৳500+', // DEMO PLACEHOLDER: verify before launch
            'description' => 'Complimentary standard delivery on orders over ৳500.',
        ],
        'secure_checkout' => 'Secure Checkout',
        'returns_policy' => '30-Day Returns on Unopened Items', // DEMO PLACEHOLDER: replace before launch
        'support_contact' => 'Client Care: concierge@the-everbloom.com', // DEMO PLACEHOLDER: replace before launch
    ],
    'socials' => [
        ['platform' => 'Instagram', 'url' => 'https://instagram.com/theeverbloom', 'handle' => '@theeverbloom'],
        ['platform' => 'Pinterest', 'url' => 'https://pinterest.com/theeverbloom', 'handle' => '@theeverbloom'],
        ['platform' => 'Facebook', 'url' => 'https://facebook.com/theeverbloom', 'handle' => 'theeverbloom'],
    ],
    'demo_discounts' => [
        'WELCOME10' => ['type' => 'percentage', 'value' => 10, 'label' => '10% Welcome Discount'],
        'BLOOM10' => ['type' => 'percentage', 'value' => 10, 'label' => '10% Welcome Discount'],
        'FREESHIP' => ['type' => 'shipping', 'value' => 100, 'label' => 'Complimentary Standard Shipping'],
        'BOTANICAL20' => ['type' => 'percentage', 'value' => 20, 'label' => '20% Seasonal Harvest Discount'],
    ],
    'shipping_rates' => [
        'standard' => [
            'id' => 'standard',
            'name' => 'Standard Delivery',
            'time' => '2–3 business days',
            'cost' => 60,
            'free_threshold' => 500,
        ],
        'express' => [
            'id' => 'express',
            'name' => 'Express Courier',
            'time' => '1–2 business days',
            'cost' => 120,
            'free_threshold' => 0,
        ],
    ],
];
