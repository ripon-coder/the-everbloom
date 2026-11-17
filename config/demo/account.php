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
        'member_since' => 'March 2024', // DEMO DATA
    ],

    /*
    |--------------------------------------------------------------------------
    | Saved Addresses
    |--------------------------------------------------------------------------
    */
    'addresses' => [
        [
            'id' => 'addr_1', // DEMO DATA
            'label' => 'Home', // DEMO DATA
            'is_default' => true, // DEMO DATA
            'recipient_name' => 'Eleanor Vance', // DEMO DATA
            'first_name' => 'Eleanor', // DEMO DATA
            'last_name' => 'Vance', // DEMO DATA
            'company' => '', // DEMO DATA
            'district' => 'Dhaka', // DEMO DATA
            'thana' => 'Dhanmondi', // DEMO DATA
            'area' => 'Dhanmondi Road 27', // DEMO DATA
            'address_line1' => 'House 18, Road 27', // DEMO DATA
            'address_line2' => '4th Floor, Flat 4B', // DEMO DATA
            'city' => 'Dhanmondi', // DEMO DATA
            'state' => 'Dhaka', // DEMO DATA
            'postal_code' => '1209', // DEMO DATA
            'country' => 'Bangladesh', // DEMO DATA
            'phone' => '01711223344', // DEMO DATA
        ],
        [
            'id' => 'addr_2', // DEMO DATA
            'label' => 'Office', // DEMO DATA
            'is_default' => false, // DEMO DATA
            'recipient_name' => 'Eleanor Vance', // DEMO DATA
            'first_name' => 'Eleanor', // DEMO DATA
            'last_name' => 'Vance', // DEMO DATA
            'company' => 'Studio Botanica', // DEMO DATA
            'district' => 'Dhaka', // DEMO DATA
            'thana' => 'Gulshan', // DEMO DATA
            'area' => 'Gulshan 1', // DEMO DATA
            'address_line1' => 'Plot 42, Road 113', // DEMO DATA
            'address_line2' => 'Level 5, Suite 5A', // DEMO DATA
            'city' => 'Gulshan', // DEMO DATA
            'state' => 'Dhaka', // DEMO DATA
            'postal_code' => '1212', // DEMO DATA
            'country' => 'Bangladesh', // DEMO DATA
            'phone' => '01819556677', // DEMO DATA
        ],
        [
            'id' => 'addr_3', // DEMO DATA
            'label' => 'Parents Home', // DEMO DATA
            'is_default' => false, // DEMO DATA
            'recipient_name' => 'Sophia Laurent', // DEMO DATA
            'first_name' => 'Sophia', // DEMO DATA
            'last_name' => 'Laurent', // DEMO DATA
            'company' => '', // DEMO DATA
            'district' => 'Dhaka', // DEMO DATA
            'thana' => 'Uttara', // DEMO DATA
            'area' => 'Uttara Sector 7', // DEMO DATA
            'address_line1' => 'House 12, Road 9', // DEMO DATA
            'address_line2' => 'Near Rabindra Sarani', // DEMO DATA
            'city' => 'Uttara', // DEMO DATA
            'state' => 'Dhaka', // DEMO DATA
            'postal_code' => '1230', // DEMO DATA
            'country' => 'Bangladesh', // DEMO DATA
            'phone' => '01912334455', // DEMO DATA
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
            'subtotal' => 1900, // DEMO DATA
            'shipping' => 0, // DEMO DATA
            'tax' => 0, // DEMO DATA
            'total' => 1900, // DEMO DATA
            'items_count' => 1, // DEMO DATA
            'delivery_method' => 'Standard delivery (3 to 5 business days)', // DEMO DATA
            'payment_method' => 'Cash on Delivery', // DEMO DATA
            'courier' => 'Steadfast Courier', // DEMO DATA
            'tracking_number' => 'Pending assignment', // DEMO DATA
            'timeline' => [
                'Placed' => 'Nov 10, 2026 · 10:15 AM', // DEMO DATA
                'Processing' => 'Nov 10, 2026 · 2:40 PM', // DEMO DATA
                'Shipped' => 'Nov 12, 2026', // DEMO DATA
                'Delivered' => 'Nov 14, 2026', // DEMO DATA
            ],
            'shipping_address' => [
                'first_name' => 'Eleanor', // DEMO DATA
                'last_name' => 'Vance', // DEMO DATA
                'address_line1' => 'House 18, Road 27', // DEMO DATA
                'area_name' => 'Dhanmondi Road 27', // DEMO DATA
                'thana_name' => 'Dhanmondi', // DEMO DATA
                'district_name' => 'Dhaka', // DEMO DATA
                'city' => 'Dhanmondi', // DEMO DATA
                'state' => 'Dhaka', // DEMO DATA
                'country' => 'Bangladesh', // DEMO DATA
                'phone' => '01711223344', // DEMO DATA
            ],
            'items' => [
                [
                    'product_id' => 1, // DEMO DATA
                    'variant_id' => '30ml / 1.0 fl oz', // DEMO DATA
                    'name' => 'Botanical Radiance Serum', // DEMO DATA
                    'variant' => '30ml / 1.0 fl oz', // DEMO DATA
                    'qty' => 1, // DEMO DATA
                    'price' => 1900, // DEMO DATA
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
            'subtotal' => 3100, // DEMO DATA
            'shipping' => 0, // DEMO DATA
            'tax' => 0, // DEMO DATA
            'total' => 3100, // DEMO DATA
            'items_count' => 2, // DEMO DATA
            'delivery_method' => 'Standard delivery (3 to 5 business days)', // DEMO DATA
            'payment_method' => 'bKash', // DEMO DATA
            'courier' => 'Pathao Courier', // DEMO DATA
            'tracking_number' => 'EVB-TRK-882019', // DEMO DATA
            'timeline' => [
                'Placed' => 'Nov 8, 2026 · 11:05 AM', // DEMO DATA
                'Processing' => 'Nov 8, 2026 · 4:20 PM', // DEMO DATA
                'Shipped' => 'Nov 9, 2026 · 9:45 AM', // DEMO DATA
                'Delivered' => 'Nov 11, 2026', // DEMO DATA
            ],
            'shipping_address' => [
                'first_name' => 'Eleanor', // DEMO DATA
                'last_name' => 'Vance', // DEMO DATA
                'address_line1' => 'Plot 42, Road 113', // DEMO DATA
                'area_name' => 'Gulshan 1', // DEMO DATA
                'thana_name' => 'Gulshan', // DEMO DATA
                'district_name' => 'Dhaka', // DEMO DATA
                'city' => 'Gulshan', // DEMO DATA
                'state' => 'Dhaka', // DEMO DATA
                'country' => 'Bangladesh', // DEMO DATA
                'phone' => '01819556677', // DEMO DATA
            ],
            'items' => [
                [
                    'product_id' => 3, // DEMO DATA
                    'variant_id' => '250ml / 8.5 fl oz', // DEMO DATA
                    'name' => 'Elderberry Gentle Cleansing Milk', // DEMO DATA
                    'variant' => '250ml / 8.5 fl oz', // DEMO DATA
                    'qty' => 1, // DEMO DATA
                    'price' => 1700, // DEMO DATA
                    'image' => 'products/elderberry-gentle-cleansing-milk-01.jpg', // DEMO DATA
                    'slug' => 'elderberry-gentle-cleansing-milk', // DEMO DATA
                ],
                [
                    'product_id' => 4, // DEMO DATA
                    'variant_id' => '50ml / 1.7 fl oz', // DEMO DATA
                    'name' => 'Mineral Shield Daily Fluid SPF 40', // DEMO DATA
                    'variant' => '50ml / 1.7 fl oz', // DEMO DATA
                    'qty' => 1, // DEMO DATA
                    'price' => 1400, // DEMO DATA
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
            'subtotal' => 3650, // DEMO DATA
            'shipping' => 0, // DEMO DATA
            'tax' => 0, // DEMO DATA
            'total' => 3650, // DEMO DATA
            'items_count' => 2, // DEMO DATA
            'delivery_method' => 'Standard delivery (3 to 5 business days)', // DEMO DATA
            'payment_method' => 'Nagad', // DEMO DATA
            'courier' => 'Steadfast Courier', // DEMO DATA
            'tracking_number' => 'EVB-TRK-984210', // DEMO DATA
            'timeline' => [
                'Placed' => 'Oct 28, 2026 · 9:30 AM', // DEMO DATA
                'Processing' => 'Oct 28, 2026 · 1:15 PM', // DEMO DATA
                'Shipped' => 'Oct 29, 2026 · 10:00 AM', // DEMO DATA
                'Delivered' => 'Oct 31, 2026 · 3:45 PM', // DEMO DATA
            ],
            'shipping_address' => [
                'first_name' => 'Eleanor', // DEMO DATA
                'last_name' => 'Vance', // DEMO DATA
                'address_line1' => 'House 18, Road 27', // DEMO DATA
                'area_name' => 'Dhanmondi Road 27', // DEMO DATA
                'thana_name' => 'Dhanmondi', // DEMO DATA
                'district_name' => 'Dhaka', // DEMO DATA
                'city' => 'Dhanmondi', // DEMO DATA
                'state' => 'Dhaka', // DEMO DATA
                'country' => 'Bangladesh', // DEMO DATA
                'phone' => '01711223344', // DEMO DATA
            ],
            'items' => [
                [
                    'product_id' => 1, // DEMO DATA
                    'variant_id' => '30ml / 1.0 fl oz', // DEMO DATA
                    'name' => 'Botanical Radiance Serum', // DEMO DATA
                    'variant' => '30ml / 1.0 fl oz', // DEMO DATA
                    'qty' => 1, // DEMO DATA
                    'price' => 1900, // DEMO DATA
                    'image' => 'products/botanical-radiance-serum-01.jpg', // DEMO DATA
                    'slug' => 'botanical-radiance-serum', // DEMO DATA
                ],
                [
                    'product_id' => 2, // DEMO DATA
                    'variant_id' => '50ml / 1.7 oz', // DEMO DATA
                    'name' => 'Camellia Restorative Moisturizer', // DEMO DATA
                    'variant' => '50ml / 1.7 oz', // DEMO DATA
                    'qty' => 1, // DEMO DATA
                    'price' => 1750, // DEMO DATA
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
            'subtotal' => 450, // DEMO DATA
            'shipping' => 60, // DEMO DATA
            'tax' => 0, // DEMO DATA
            'total' => 510, // DEMO DATA
            'items_count' => 1, // DEMO DATA
            'delivery_method' => 'Standard delivery (3 to 5 business days)', // DEMO DATA
            'payment_method' => 'Cash on Delivery', // DEMO DATA
            'courier' => 'Pathao Courier', // DEMO DATA
            'tracking_number' => 'EVB-TRK-740201', // DEMO DATA
            'timeline' => [
                'Placed' => 'Sep 15, 2026 · 2:10 PM', // DEMO DATA
                'Processing' => 'Sep 15, 2026 · 5:00 PM', // DEMO DATA
                'Shipped' => 'Sep 16, 2026 · 11:20 AM', // DEMO DATA
                'Delivered' => 'Sep 18, 2026 · 4:05 PM', // DEMO DATA
            ],
            'shipping_address' => [
                'first_name' => 'Sophia', // DEMO DATA
                'last_name' => 'Laurent', // DEMO DATA
                'address_line1' => 'House 12, Road 9', // DEMO DATA
                'area_name' => 'Uttara Sector 7', // DEMO DATA
                'thana_name' => 'Uttara', // DEMO DATA
                'district_name' => 'Dhaka', // DEMO DATA
                'city' => 'Uttara', // DEMO DATA
                'state' => 'Dhaka', // DEMO DATA
                'country' => 'Bangladesh', // DEMO DATA
                'phone' => '01912334455', // DEMO DATA
            ],
            'items' => [
                [
                    'product_id' => 4, // DEMO DATA
                    'variant_id' => '50ml / 1.7 fl oz', // DEMO DATA
                    'name' => 'Mineral Shield Daily Fluid SPF 40', // DEMO DATA
                    'variant' => '50ml / 1.7 fl oz', // DEMO DATA
                    'qty' => 1, // DEMO DATA
                    'price' => 450, // DEMO DATA
                    'image' => 'products/mineral-shield-daily-fluid-spf40-01.jpg', // DEMO DATA
                    'slug' => 'mineral-shield-daily-fluid-spf40', // DEMO DATA
                ],
            ],
        ],
    ],
];
