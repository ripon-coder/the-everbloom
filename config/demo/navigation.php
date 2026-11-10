<?php

return [
    'main' => [
        ['label' => 'Shop All', 'url' => '/shop'],
        ['label' => 'Face Care', 'url' => '/shop/category/face-care'],
        ['label' => 'Serums', 'url' => '/shop/category/serums'],
        ['label' => 'Moisturizers', 'url' => '/shop/category/moisturizers'],
        ['label' => 'Cleansers', 'url' => '/shop/category/cleansers'],
        ['label' => 'Body Care', 'url' => '/shop/category/body-care'],
        ['label' => 'Our Story', 'url' => '/about'],
    ],
    'categories' => [
        ['label' => 'Face Care', 'url' => '/shop/category/face-care', 'description' => 'Gentle botanicals for balanced radiant skin'],
        ['label' => 'Serums', 'url' => '/shop/category/serums', 'description' => 'Concentrated active botanicals & extracts'],
        ['label' => 'Moisturizers', 'url' => '/shop/category/moisturizers', 'description' => 'Barrier-supporting creams & gels'],
        ['label' => 'Cleansers', 'url' => '/shop/category/cleansers', 'description' => 'Balms, milk washes, and jelly purifiers'],
        ['label' => 'Body Care', 'url' => '/shop/category/body-care', 'description' => 'Hydrating body butters & dry satin oils'],
        ['label' => 'Sunscreens', 'url' => '/shop/category/sunscreens', 'description' => 'Mineral-based non-nano daily SPF protection'],
    ],
    'footer' => [
        'shop' => [
            'title' => 'Botanical Offerings',
            'links' => [
                ['label' => 'Shop All Products', 'url' => '/shop'],
                ['label' => 'Face Care Collection', 'url' => '/shop/category/face-care'],
                ['label' => 'Concentrated Serums', 'url' => '/shop/category/serums'],
                ['label' => 'Moisturizers & Balms', 'url' => '/shop/category/moisturizers'],
                ['label' => 'Cleansers & Washes', 'url' => '/shop/category/cleansers'],
                ['label' => 'Mineral Sun Care', 'url' => '/shop/category/sunscreens'],
            ],
        ],
        'about' => [
            'title' => 'The House',
            'links' => [
                ['label' => 'Our Botanical Philosophy', 'url' => '/about'],
                ['label' => 'Conscious Sourcing', 'url' => '/about#sourcing'],
                ['label' => 'Customer Testimonials', 'url' => '/#testimonials'],
                ['label' => 'Journal & Botanical Notes', 'url' => '/about#journal'],
            ],
        ],
        'help' => [
            'title' => 'Concierge & Care',
            'links' => [
                ['label' => 'Client Care & Contact', 'url' => '/contact'],
                ['label' => 'Frequently Asked Questions', 'url' => '/faq'],
                ['label' => 'Shipping & Botanical Delivery', 'url' => '/shipping-and-returns'],
                ['label' => 'Returns & Exchanges', 'url' => '/shipping-and-returns#returns'],
                ['label' => 'Track Your Order', 'url' => '/account/orders'],
            ],
        ],
        'legal' => [
            'title' => 'Legal & Terms',
            'links' => [
                ['label' => 'Privacy Policy', 'url' => '/privacy-policy'],
                ['label' => 'Terms of Service', 'url' => '/terms-and-conditions'],
                ['label' => 'Accessibility Statement', 'url' => '/terms-and-conditions#accessibility'],
                ['label' => 'Cookie Settings', 'url' => '/privacy-policy#cookies'],
            ],
        ],
    ],
];
