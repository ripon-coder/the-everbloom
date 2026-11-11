<?php

return [
    'name' => 'The Everbloom',
    'tagline' => 'Naturally Beautiful. Always Blooming.',
    'url' => 'https://the-everbloom.com',
    'currency' => [
        'code' => 'USD',
        'symbol' => '$',
        'position' => 'before', // before or after
        'decimals' => 2,
    ],
    'announcement' => [
        'enabled' => true,
        'message' => 'Free shipping on orders over $75',
        'link' => '/shop',
        'link_text' => 'Shop Now',
    ],
    // DEMO PLACEHOLDER: replace before launch
    'shipping_and_returns_note' => 'Free standard shipping over $75. 30-day returns on unopened items.', // DEMO PLACEHOLDER: replace before launch
    'shipping' => [
        'free_shipping_threshold' => 75.00, // DEMO PLACEHOLDER: replace before launch
        'standard_rate' => 6.00,            // DEMO PLACEHOLDER: replace before launch
        'express_rate' => 15.00,            // DEMO PLACEHOLDER: replace before launch
        'standard_days' => '3–5 business days', // DEMO PLACEHOLDER: replace before launch
        'express_days' => '1–2 business days',  // DEMO PLACEHOLDER: replace before launch
    ],
    'returns' => [
        'window_days' => 30, // DEMO PLACEHOLDER: replace before launch
    ],
    // DEMO PLACEHOLDER: replace before launch
    'contact' => [
        'email' => 'concierge@the-everbloom.com', // DEMO PLACEHOLDER: replace before launch
        'phone' => '+1 (800) 412-8874',           // DEMO PLACEHOLDER: replace before launch
        'hours' => 'Mon – Fri: 9:00 AM – 6:00 PM EST', // DEMO PLACEHOLDER: replace before launch
        'address' => '740 Botanical Way, Suite 400, Portland, OR 97201', // DEMO PLACEHOLDER: replace before launch
    ],
    // DEMO PLACEHOLDER: replace before launch
    'trust_badges' => [
        [
            'icon' => 'leaf',
            'title' => 'Plant-Derived Actives',
            'description' => 'Pure botanical oils, hydrosols, and barrier-supportive extracts.',
        ],
        [
            'icon' => 'heart',
            'title' => 'Cruelty-Free Always',
            'description' => 'Leaping Bunny certified and 100% vegan formulations.',
        ],
        [
            'icon' => 'globe',
            'title' => 'Amber Glass Vessels',
            'description' => 'Recyclable UV-protective glass to preserve potency.',
        ],
        [
            'icon' => 'shield',
            'title' => 'Free Shipping $75+',
            'description' => 'Complimentary standard delivery on orders over $75.',
        ],
        'secure_checkout' => 'Secure Checkout (Demo)', // DEMO PLACEHOLDER: replace before launch
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
            'name' => 'Standard Ground Botanical Shipping',
            'time' => '3–5 business days',
            'cost' => 6.00,
            'free_threshold' => 75.00,
        ],
        'express' => [
            'id' => 'express',
            'name' => 'Express Priority Courier',
            'time' => '1–2 business days',
            'cost' => 15.00,
            'free_threshold' => 150.00,
        ],
    ],
];
