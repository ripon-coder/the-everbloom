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
        'message' => 'Complimentary botanical discovery duo on orders over $75 · Worldwide insured shipping',
        'link' => '/shop',
        'link_text' => 'Shop Now',
    ],
    // DEMO: Placeholder contact details for frontend presentation
    'contact' => [
        'email' => 'concierge@the-everbloom.com', // DEMO: replace with real contact email
        'phone' => '+1 (800) 412-8874',           // DEMO: replace with real customer phone
        'hours' => 'Mon – Fri: 9:00 AM – 6:00 PM EST', // DEMO: replace with real operating hours
        'address' => '740 Botanical Way, Suite 400, Portland, OR 97201', // DEMO: placeholder address
    ],
    'socials' => [
        ['platform' => 'Instagram', 'url' => 'https://instagram.com/theeverbloom', 'handle' => '@theeverbloom'],
        ['platform' => 'Pinterest', 'url' => 'https://pinterest.com/theeverbloom', 'handle' => '@theeverbloom'],
        ['platform' => 'Facebook', 'url' => 'https://facebook.com/theeverbloom', 'handle' => 'theeverbloom'],
    ],
    'demo_discounts' => [
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
            'free_threshold' => 65.00,
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
