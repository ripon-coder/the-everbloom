<?php

// DEMO DATA — The Everbloom Demonstration Customer & Order Records
// BACKEND: replace with database models (User, Order, Address)

return [
    /*
    |--------------------------------------------------------------------------
    | Demonstration Customer Profile
    |--------------------------------------------------------------------------
    */
    'customer' => [
        'first_name' => 'Eleanor', // DEMO DATA
        'last_name' => 'Vance', // DEMO DATA
        'email' => 'eleanor.vance@example.com', // DEMO DATA
        'phone' => '+1 (503) 555-0192', // DEMO DATA
    ],

    /*
    |--------------------------------------------------------------------------
    | Saved Addresses
    |--------------------------------------------------------------------------
    */
    'addresses' => [
        [
            'id' => 'addr_1', // DEMO DATA
            'is_default' => true, // DEMO DATA
            'first_name' => 'Eleanor', // DEMO DATA
            'last_name' => 'Vance', // DEMO DATA
            'company' => '', // DEMO DATA
            'address_line1' => '740 Botanical Way', // DEMO DATA
            'address_line2' => 'Suite 400', // DEMO DATA
            'city' => 'Portland', // DEMO DATA
            'state' => 'OR', // DEMO DATA
            'postal_code' => '97201', // DEMO DATA
            'country' => 'United States', // DEMO DATA
            'phone' => '+1 (503) 555-0192', // DEMO DATA
        ],
        [
            'id' => 'addr_2', // DEMO DATA
            'is_default' => false, // DEMO DATA
            'first_name' => 'Eleanor', // DEMO DATA
            'last_name' => 'Vance', // DEMO DATA
            'company' => 'Studio Botanica', // DEMO DATA
            'address_line1' => '1284 Rosewood Terrace', // DEMO DATA
            'address_line2' => 'Apt 2B', // DEMO DATA
            'city' => 'Seattle', // DEMO DATA
            'state' => 'WA', // DEMO DATA
            'postal_code' => '98101', // DEMO DATA
            'country' => 'United States', // DEMO DATA
            'phone' => '+1 (206) 555-0144', // DEMO DATA
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Customer Orders (4 orders with full item details and status steps)
    |--------------------------------------------------------------------------
    */
    'orders' => [
        [
            'id' => 'EVB-9204', // DEMO DATA
            'date' => 'November 10, 2026', // DEMO DATA
            'iso_date' => '2026-11-10', // DEMO DATA
            'status' => 'Processing', // DEMO DATA
            'current_step' => 'Processing', // DEMO DATA: Placed -> Processing -> Shipped -> Delivered
            'subtotal' => 64.00, // DEMO DATA
            'shipping' => 0.00, // DEMO DATA
            'tax' => 0.00, // DEMO DATA
            'total' => 64.00, // DEMO DATA
            'items_count' => 1, // DEMO DATA
            'delivery_method' => 'Complimentary Standard Ground (3–5 business days)', // DEMO DATA
            'payment_method' => 'Apple Pay (•••• 9840)', // DEMO DATA
            'tracking_number' => 'Pending assignment', // DEMO DATA
            'shipping_address' => [
                'first_name' => 'Eleanor', // DEMO DATA
                'last_name' => 'Vance', // DEMO DATA
                'address_line1' => '740 Botanical Way', // DEMO DATA
                'address_line2' => 'Suite 400', // DEMO DATA
                'city' => 'Portland', // DEMO DATA
                'state' => 'OR', // DEMO DATA
                'postal_code' => '97201', // DEMO DATA
                'country' => 'United States', // DEMO DATA
                'phone' => '+1 (503) 555-0192', // DEMO DATA
            ],
            'items' => [
                [
                    'product_id' => 1, // DEMO DATA
                    'variant_id' => '30ml / 1.0 fl oz', // DEMO DATA
                    'name' => 'Botanical Radiance Serum', // DEMO DATA
                    'variant' => '30ml / 1.0 fl oz', // DEMO DATA
                    'qty' => 1, // DEMO DATA
                    'price' => 64.00, // DEMO DATA
                    'image' => 'products/botanical-radiance-serum-01.jpg', // DEMO DATA
                    'slug' => 'botanical-radiance-serum', // DEMO DATA
                ],
            ],
        ],
        [
            'id' => 'EVB-9145', // DEMO DATA
            'date' => 'November 8, 2026', // DEMO DATA
            'iso_date' => '2026-11-08', // DEMO DATA
            'status' => 'Shipped', // DEMO DATA
            'current_step' => 'Shipped', // DEMO DATA
            'subtotal' => 102.00, // DEMO DATA
            'shipping' => 0.00, // DEMO DATA
            'tax' => 0.00, // DEMO DATA
            'total' => 102.00, // DEMO DATA
            'items_count' => 2, // DEMO DATA
            'delivery_method' => 'Botanical Express Courier (1–2 business days)', // DEMO DATA
            'payment_method' => 'Visa ending in 8819', // DEMO DATA
            'tracking_number' => 'EVB-TRK-882019', // DEMO DATA
            'shipping_address' => [
                'first_name' => 'Eleanor', // DEMO DATA
                'last_name' => 'Vance', // DEMO DATA
                'address_line1' => '740 Botanical Way', // DEMO DATA
                'address_line2' => 'Suite 400', // DEMO DATA
                'city' => 'Portland', // DEMO DATA
                'state' => 'OR', // DEMO DATA
                'postal_code' => '97201', // DEMO DATA
                'country' => 'United States', // DEMO DATA
                'phone' => '+1 (503) 555-0192', // DEMO DATA
            ],
            'items' => [
                [
                    'product_id' => 3, // DEMO DATA
                    'variant_id' => '250ml / 8.5 fl oz', // DEMO DATA
                    'name' => 'Elderberry Gentle Cleansing Milk', // DEMO DATA
                    'variant' => '250ml / 8.5 fl oz', // DEMO DATA
                    'qty' => 1, // DEMO DATA
                    'price' => 56.00, // DEMO DATA
                    'image' => 'products/elderberry-gentle-cleansing-milk-01.jpg', // DEMO DATA
                    'slug' => 'elderberry-gentle-cleansing-milk', // DEMO DATA
                ],
                [
                    'product_id' => 4, // DEMO DATA
                    'variant_id' => '50ml / 1.7 fl oz', // DEMO DATA
                    'name' => 'Mineral Shield Daily Fluid SPF 40', // DEMO DATA
                    'variant' => '50ml / 1.7 fl oz', // DEMO DATA
                    'qty' => 1, // DEMO DATA
                    'price' => 46.00, // DEMO DATA
                    'image' => 'products/mineral-shield-daily-fluid-spf40-01.jpg', // DEMO DATA
                    'slug' => 'mineral-shield-daily-fluid-spf40', // DEMO DATA
                ],
            ],
        ],
        [
            'id' => 'EVB-8921', // DEMO DATA
            'date' => 'October 28, 2026', // DEMO DATA
            'iso_date' => '2026-10-28', // DEMO DATA
            'status' => 'Delivered', // DEMO DATA
            'current_step' => 'Delivered', // DEMO DATA
            'subtotal' => 122.00, // DEMO DATA
            'shipping' => 0.00, // DEMO DATA
            'tax' => 0.00, // DEMO DATA
            'total' => 122.00, // DEMO DATA
            'items_count' => 2, // DEMO DATA
            'delivery_method' => 'Complimentary Standard Ground (3–5 business days)', // DEMO DATA
            'payment_method' => 'Mastercard ending in 4242', // DEMO DATA
            'tracking_number' => 'EVB-TRK-984210', // DEMO DATA
            'shipping_address' => [
                'first_name' => 'Eleanor', // DEMO DATA
                'last_name' => 'Vance', // DEMO DATA
                'address_line1' => '740 Botanical Way', // DEMO DATA
                'address_line2' => 'Suite 400', // DEMO DATA
                'city' => 'Portland', // DEMO DATA
                'state' => 'OR', // DEMO DATA
                'postal_code' => '97201', // DEMO DATA
                'country' => 'United States', // DEMO DATA
                'phone' => '+1 (503) 555-0192', // DEMO DATA
            ],
            'items' => [
                [
                    'product_id' => 1, // DEMO DATA
                    'variant_id' => '30ml / 1.0 fl oz', // DEMO DATA
                    'name' => 'Botanical Radiance Serum', // DEMO DATA
                    'variant' => '30ml / 1.0 fl oz', // DEMO DATA
                    'qty' => 1, // DEMO DATA
                    'price' => 64.00, // DEMO DATA
                    'image' => 'products/botanical-radiance-serum-01.jpg', // DEMO DATA
                    'slug' => 'botanical-radiance-serum', // DEMO DATA
                ],
                [
                    'product_id' => 2, // DEMO DATA
                    'variant_id' => '50ml / 1.7 oz', // DEMO DATA
                    'name' => 'Camellia Restorative Moisturizer', // DEMO DATA
                    'variant' => '50ml / 1.7 oz', // DEMO DATA
                    'qty' => 1, // DEMO DATA
                    'price' => 58.00, // DEMO DATA
                    'image' => 'products/camellia-restorative-moisturizer-01.jpg', // DEMO DATA
                    'slug' => 'camellia-restorative-moisturizer', // DEMO DATA
                ],
            ],
        ],
        [
            'id' => 'EVB-7402', // DEMO DATA
            'date' => 'September 15, 2026', // DEMO DATA
            'iso_date' => '2026-09-15', // DEMO DATA
            'status' => 'Delivered', // DEMO DATA
            'current_step' => 'Delivered', // DEMO DATA
            'subtotal' => 46.00, // DEMO DATA
            'shipping' => 0.00, // DEMO DATA
            'tax' => 0.00, // DEMO DATA
            'total' => 46.00, // DEMO DATA
            'items_count' => 1, // DEMO DATA
            'delivery_method' => 'Complimentary Standard Ground (3–5 business days)', // DEMO DATA
            'payment_method' => 'Mastercard ending in 4242', // DEMO DATA
            'tracking_number' => 'EVB-TRK-740201', // DEMO DATA
            'shipping_address' => [
                'first_name' => 'Eleanor', // DEMO DATA
                'last_name' => 'Vance', // DEMO DATA
                'address_line1' => '740 Botanical Way', // DEMO DATA
                'address_line2' => 'Suite 400', // DEMO DATA
                'city' => 'Portland', // DEMO DATA
                'state' => 'OR', // DEMO DATA
                'postal_code' => '97201', // DEMO DATA
                'country' => 'United States', // DEMO DATA
                'phone' => '+1 (503) 555-0192', // DEMO DATA
            ],
            'items' => [
                [
                    'product_id' => 4, // DEMO DATA
                    'variant_id' => '50ml / 1.7 fl oz', // DEMO DATA
                    'name' => 'Mineral Shield Daily Fluid SPF 40', // DEMO DATA
                    'variant' => '50ml / 1.7 fl oz', // DEMO DATA
                    'qty' => 1, // DEMO DATA
                    'price' => 46.00, // DEMO DATA
                    'image' => 'products/mineral-shield-daily-fluid-spf40-01.jpg', // DEMO DATA
                    'slug' => 'mineral-shield-daily-fluid-spf40', // DEMO DATA
                ],
            ],
        ],
    ],
];
