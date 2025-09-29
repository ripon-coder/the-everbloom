<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Constants\AttributeValueStatus;
use Illuminate\Database\Seeder;

class AttributeValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing attribute values
        AttributeValue::query()->delete();

        // Get all attributes
        $attributes = Attribute::all();

        if ($attributes->count() === 0) {
            $this->command->warn('No attributes found. Please run AttributeSeeder first.');
            return;
        }

        // Create attribute values for each attribute
        foreach ($attributes as $attribute) {
            $this->createAttributeValues($attribute);
        }

        $this->command->info('Attribute values seeded successfully!');
    }

    /**
     * Create attribute values for a specific attribute.
     */
    private function createAttributeValues(Attribute $attribute): void
    {
        $attributeValues = [];

        switch ($attribute->name) {
            case 'Color':
                $attributeValues = [
                    ['value' => 'Red', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'Blue', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'Green', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'Black', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'White', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'Yellow', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'Purple', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'Orange', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'Pink', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'Gray', 'status' => AttributeValueStatus::ACTIVE],
                ];
                break;

            case 'Size':
                $attributeValues = [
                    ['value' => 'XS', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'S', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'M', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'L', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'XL', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'XXL', 'status' => AttributeValueStatus::ACTIVE],
                ];
                break;

            case 'Material':
                $attributeValues = [
                    ['value' => 'Cotton', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'Polyester', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'Wool', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'Silk', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'Leather', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'Denim', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'Nylon', 'status' => AttributeValueStatus::ACTIVE],
                ];
                break;

            case 'Storage':
                $attributeValues = [
                    ['value' => '32GB', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => '64GB', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => '128GB', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => '256GB', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => '512GB', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => '1TB', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => '2TB', 'status' => AttributeValueStatus::ACTIVE],
                ];
                break;

            case 'RAM':
                $attributeValues = [
                    ['value' => '4GB', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => '8GB', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => '16GB', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => '32GB', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => '64GB', 'status' => AttributeValueStatus::ACTIVE],
                ];
                break;

            case 'Screen Size':
                $attributeValues = [
                    ['value' => '13"', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => '14"', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => '15.6"', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => '17"', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => '24"', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => '27"', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => '32"', 'status' => AttributeValueStatus::ACTIVE],
                ];
                break;

            case 'Weight':
                $attributeValues = [
                    ['value' => 'Light', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'Medium', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'Heavy', 'status' => AttributeValueStatus::ACTIVE],
                ];
                break;

            case 'Warranty':
                $attributeValues = [
                    ['value' => '3 Months', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => '6 Months', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => '1 Year', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => '2 Years', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => '3 Years', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => '5 Years', 'status' => AttributeValueStatus::ACTIVE],
                ];
                break;

            case 'Connectivity':
                $attributeValues = [
                    ['value' => 'Wired', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'Wireless', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'Bluetooth', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'WiFi', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'USB-C', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'HDMI', 'status' => AttributeValueStatus::ACTIVE],
                ];
                break;

            default:
                // For any other attributes, create some generic values
                $attributeValues = [
                    ['value' => 'Option 1', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'Option 2', 'status' => AttributeValueStatus::ACTIVE],
                    ['value' => 'Option 3', 'status' => AttributeValueStatus::ACTIVE],
                ];
                break;
        }

        // Create the attribute values
        foreach ($attributeValues as $valueData) {
            AttributeValue::create([
                'attribute_id' => $attribute->id,
                'value' => $valueData['value'],
                'status' => $valueData['status'],
            ]);
        }
    }
}
