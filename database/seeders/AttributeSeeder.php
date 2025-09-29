<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Constants\AttributeStatus;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing attributes
        Attribute::query()->delete();

        // Sample attributes data
        $attributes = [
            [
                'name' => 'Color',
                'description' => 'Product color options',
                'is_image' => true,
                'status' => AttributeStatus::ACTIVE,
            ],
            [
                'name' => 'Size',
                'description' => 'Product size options',
                'is_image' => false,
                'status' => AttributeStatus::ACTIVE,
            ],
            [
                'name' => 'Material',
                'description' => 'Product material options',
                'is_image' => false,
                'status' => AttributeStatus::ACTIVE,
            ],
            [
                'name' => 'Storage',
                'description' => 'Storage capacity options',
                'is_image' => false,
                'status' => AttributeStatus::ACTIVE,
            ],
            [
                'name' => 'RAM',
                'description' => 'Memory options',
                'is_image' => false,
                'status' => AttributeStatus::ACTIVE,
            ],
            [
                'name' => 'Screen Size',
                'description' => 'Display size options',
                'is_image' => false,
                'status' => AttributeStatus::ACTIVE,
            ],
            [
                'name' => 'Weight',
                'description' => 'Product weight options',
                'is_image' => false,
                'status' => AttributeStatus::ACTIVE,
            ],
            [
                'name' => 'Warranty',
                'description' => 'Warranty period options',
                'is_image' => false,
                'status' => AttributeStatus::ACTIVE,
            ],
            [
                'name' => 'Connectivity',
                'description' => 'Connectivity options',
                'is_image' => false,
                'status' => AttributeStatus::ACTIVE,
            ],
        ];

        // Create attributes
        foreach ($attributes as $attributeData) {
            Attribute::create($attributeData);
        }

        $this->command->info(count($attributes) . ' attributes seeded successfully!');
    }
}
