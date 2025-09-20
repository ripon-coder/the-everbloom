<?php

namespace Database\Seeders;

use App\Constants\BrandStatus;
use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "name" => "Sony",
                "slug" => "sony",
                "description"=>"lorem ipsum somethng",
                "options" => "",
                "status" => BrandStatus::ACTIVE
            ],
            [
                "name" => "Nokia",
                "slug" => "nokia",
                "description"=>"lorem ipsum somethng",
                "options" => "",
                "status" => BrandStatus::ACTIVE
            ],
            [
                "name" => "Samsung",
                "slug" => "samsung",
                "description"=>"lorem ipsum somethng",
                "options" => "",
                "status" => BrandStatus::ACTIVE
            ],
            [
                "name" => "Apple",
                "slug" => "apple",
                "description"=>"lorem ipsum somethng",
                "options" => "",
                "status" => BrandStatus::ACTIVE
            ],
            [
                "name" => "Xaiomi",
                "slug" => "xaimi",
                "description"=>"lorem ipsum somethng",
                "options" => "",
                "status" => BrandStatus::ACTIVE
            ]
        ];

        foreach ($data as $item) {
            Brand::create($item);
        }

    }
}
