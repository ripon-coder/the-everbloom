# Variant Attribute Pivot Table Implementation

## Task Checklist
- [x] Create migration for variant_attributes table
- [x] Create VariantAttribute model with fillable fields
- [x] Define relationships in VariantAttribute model
- [x] Add hasMany relationship to ProductVariant model
- [x] Add hasMany relationship to Attribute model
- [x] Add hasMany relationship to AttributeValue model
- [x] Verify all relationships are properly defined

## Implementation Summary

### Files Created:
1. `database/migrations/2025_09_22_134700_create_product_variants_table.php` - Migration for product_variants table
2. `database/migrations/2025_09_22_134800_create_variant_attributes_table.php` - Migration for variant_attributes pivot table
3. `app/Models/ProductVariant.php` - ProductVariant model
4. `app/Models/VariantAttribute.php` - VariantAttribute pivot model

### Files Updated:
1. `app/Models/Attribute.php` - Added variantAttributes() relationship
2. `app/Models/AttributeValue.php` - Added variantAttributes() relationship
3. `app/Models/Product.php` - Added variants() relationship

### Database Schema:
- **product_variants**: id, product_id, name, sku, price, stock, image, timestamps, softDeletes
- **variant_attributes**: id, product_variant_id, attribute_id, attribute_value_id, timestamps

### Relationships Implemented:
- **VariantAttribute** (pivot):
  - belongsTo(ProductVariant::class)
  - belongsTo(Attribute::class)
  - belongsTo(AttributeValue::class)
- **ProductVariant**:
  - belongsTo(Product::class)
  - hasMany(VariantAttribute::class)
- **Attribute**:
  - hasMany(VariantAttribute::class)
- **AttributeValue**:
  - hasMany(VariantAttribute::class)
- **Product**:
  - hasMany(ProductVariant::class)

### Usage Example:
```php
// Get all attributes for a product variant
$variant = ProductVariant::find(1);
$variantAttributes = $variant->variantAttributes;

foreach ($variantAttributes as $variantAttribute) {
    $attributeName = $variantAttribute->attribute->name; // e.g., "Color"
    $attributeValue = $variantAttribute->attributeValue->value; // e.g., "Red"
}

// Get all variants that have a specific attribute value
$attributeValue = AttributeValue::find(1);
$variants = $attributeValue->variantAttributes()->with('productVariant')->get();
```
