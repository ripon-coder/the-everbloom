<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantAttribute;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Constants\ProductVariantStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data
        ProductVariant::query()->delete();
        VariantAttribute::query()->delete();
        
        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get products, attributes, and attribute values
        $products = Product::all();
        $attributes = Attribute::all();
        
        if ($products->count() === 0) {
            $this->command->warn('No products found. Please run ProductSeeder first.');
            return;
        }

        if ($attributes->count() === 0) {
            $this->command->warn('No attributes found. Please run AttributeSeeder first.');
            return;
        }

        // Get attribute values for each attribute
        $attributeValues = [];
        foreach ($attributes as $attribute) {
            $attributeValues[$attribute->id] = $attribute->attributeValues()->get();
        }

        // Create product variants
        $variantsCreated = 0;
        
        foreach ($products as $product) {
            // Create variants for electronics products
            if (in_array($product->name, [
                'Wireless Bluetooth Headphones',
                'Power Bank 10000mAh',
                'Bluetooth Speaker',
                'Webcam HD 1080p'
            ])) {
                $variantsCreated += $this->createElectronicsVariants($product, $attributes, $attributeValues);
            }
            
            // Create variants for accessories
            if (in_array($product->name, [
                'Smartphone Case',
                'USB-C Charging Cable',
                'Phone Screen Protector',
                'Wireless Charger',
                'Tablet Stylus Pen'
            ])) {
                $variantsCreated += $this->createAccessoriesVariants($product, $attributes, $attributeValues);
            }
            
            // Create variants for computer peripherals
            if (in_array($product->name, [
                'Wireless Mouse',
                'Laptop Stand',
                'Gaming Keyboard'
            ])) {
                $variantsCreated += $this->createPeripheralsVariants($product, $attributes, $attributeValues);
            }
        }

        $this->command->info($variantsCreated . ' product variants seeded successfully!');
    }

    /**
     * Create variants for electronics products.
     */
    private function createElectronicsVariants(Product $product, $attributes, $attributeValues): int
    {
        $variantsCreated = 0;
        
        // Get relevant attributes for electronics
        $colorAttr = $attributes->firstWhere('name', 'Color');
        $storageAttr = $attributes->firstWhere('name', 'Storage');
        $warrantyAttr = $attributes->firstWhere('name', 'Warranty');
        
        $colorValues = $colorAttr ? $attributeValues[$colorAttr->id] : collect();
        $storageValues = $storageAttr ? $attributeValues[$storageAttr->id] : collect();
        $warrantyValues = $warrantyAttr ? $attributeValues[$warrantyAttr->id] : collect();
        
        // Create combinations
        $selectedColors = $colorValues->take(3); // Limit to 3 colors
        $selectedStorage = $storageValues->take(2); // Limit to 2 storage options
        $selectedWarranty = $warrantyValues->take(2); // Limit to 2 warranty options
        
        foreach ($selectedColors as $color) {
            foreach ($selectedStorage as $storage) {
                foreach ($selectedWarranty as $warranty) {
                    $variant = $this->createVariant($product, [
                        'color' => $color,
                        'storage' => $storage,
                        'warranty' => $warranty,
                    ]);
                    
                    if ($variant) {
                        $variantsCreated++;
                    }
                }
            }
        }
        
        return $variantsCreated;
    }

    /**
     * Create variants for accessories products.
     */
    private function createAccessoriesVariants(Product $product, $attributes, $attributeValues): int
    {
        $variantsCreated = 0;
        
        // Get relevant attributes for accessories
        $colorAttr = $attributes->firstWhere('name', 'Color');
        $sizeAttr = $attributes->firstWhere('name', 'Size');
        $materialAttr = $attributes->firstWhere('name', 'Material');
        
        $colorValues = $colorAttr ? $attributeValues[$colorAttr->id] : collect();
        $sizeValues = $sizeAttr ? $attributeValues[$sizeAttr->id] : collect();
        $materialValues = $materialAttr ? $attributeValues[$materialAttr->id] : collect();
        
        // Create combinations
        $selectedColors = $colorValues->take(4); // Limit to 4 colors
        $selectedSizes = $sizeValues->take(3); // Limit to 3 sizes
        $selectedMaterials = $materialValues->take(2); // Limit to 2 materials
        
        foreach ($selectedColors as $color) {
            foreach ($selectedSizes as $size) {
                foreach ($selectedMaterials as $material) {
                    $variant = $this->createVariant($product, [
                        'color' => $color,
                        'size' => $size,
                        'material' => $material,
                    ]);
                    
                    if ($variant) {
                        $variantsCreated++;
                    }
                }
            }
        }
        
        return $variantsCreated;
    }

    /**
     * Create variants for computer peripherals.
     */
    private function createPeripheralsVariants(Product $product, $attributes, $attributeValues): int
    {
        $variantsCreated = 0;
        
        // Get relevant attributes for peripherals
        $colorAttr = $attributes->firstWhere('name', 'Color');
        $connectivityAttr = $attributes->firstWhere('name', 'Connectivity');
        $warrantyAttr = $attributes->firstWhere('name', 'Warranty');
        
        $colorValues = $colorAttr ? $attributeValues[$colorAttr->id] : collect();
        $connectivityValues = $connectivityAttr ? $attributeValues[$connectivityAttr->id] : collect();
        $warrantyValues = $warrantyAttr ? $attributeValues[$warrantyAttr->id] : collect();
        
        // Create combinations
        $selectedColors = $colorValues->take(3); // Limit to 3 colors
        $selectedConnectivity = $connectivityValues->take(2); // Limit to 2 connectivity options
        $selectedWarranty = $warrantyValues->take(2); // Limit to 2 warranty options
        
        foreach ($selectedColors as $color) {
            foreach ($selectedConnectivity as $connectivity) {
                foreach ($selectedWarranty as $warranty) {
                    $variant = $this->createVariant($product, [
                        'color' => $color,
                        'connectivity' => $connectivity,
                        'warranty' => $warranty,
                    ]);
                    
                    if ($variant) {
                        $variantsCreated++;
                    }
                }
            }
        }
        
        return $variantsCreated;
    }

    /**
     * Create a single product variant with attributes.
     */
    private function createVariant(Product $product, array $attributeData): ?ProductVariant
    {
        try {
            // Generate SKU
            $skuParts = [$product->id];
            $variantNameParts = [$product->name];
            
            foreach ($attributeData as $key => $attributeValue) {
                $skuParts[] = strtoupper(substr($attributeValue->value, 0, 3));
                $variantNameParts[] = $attributeValue->value;
            }
            
            $sku = 'VAR-' . implode('-', $skuParts) . '-' . rand(100, 999);
            $variantName = implode(' ', $variantNameParts);
            
            // Calculate prices
            $basePrice = $product->price;
            $priceModifier = rand(-10, 30); // Price can vary from -10 to +30
            $sellPrice = max(0.01, $basePrice + $priceModifier);
            $buyingPrice = $sellPrice * 0.6; // Buying price is 60% of sell price
            $discountPrice = rand(0, 10) === 1 ? $sellPrice * 0.9 : null; // 10% chance of discount
            
            // Create the variant
            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'sku' => $sku,
                'buying_price' => $buyingPrice,
                'sell_price' => $sellPrice,
                'discount_price' => $discountPrice,
                'stock' => rand(0, 100),
                'status' => ProductVariantStatus::ACTIVE,
                'track_stock' => true,
            ]);
            
            // Create variant attributes
            foreach ($attributeData as $key => $attributeValue) {
                VariantAttribute::create([
                    'product_variant_id' => $variant->id,
                    'attribute_id' => $attributeValue->attribute_id,
                    'attribute_value_id' => $attributeValue->id,
                ]);
            }
            
            return $variant;
        } catch (\Exception $e) {
            $this->command->error("Error creating variant for product {$product->id}: {$e->getMessage()}");
            return null;
        }
    }
}
