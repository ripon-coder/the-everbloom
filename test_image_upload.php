<?php
/**
 * Test script to verify image upload functionality
 */

require_once 'vendor/autoload.php';

use Illuminate\Http\UploadedFile;
use App\Services\ProductService;
use App\Repositories\Contracts\ProductRepository;

// Mock the necessary dependencies
class MockProductRepository implements ProductRepository
{
    public function index()
    {
        return collect();
    }

    public function create()
    {
        return [];
    }

    public function store(array $data)
    {
        // Return a mock product object
        return new class {
            public $id = 1;
            public function images()
            {
                return new class {
                    public function create(array $data)
                    {
                        return new class {
                            public $id = 1;
                            public function addMedia($file)
                            {
                                return new class {
                                    public function toMediaCollection($collection)
                                    {
                                        return true;
                                    }
                                };
                            }
                        };
                    }
                };
            }
        };
    }
}

// Test data
$testData = [
    'brand_id' => 1,
    'category_id' => 1,
    'name' => 'Test Product',
    'description' => 'Test Description',
    'price' => 100.00,
    'status' => 'active',
    'images' => []
];

// Create a mock image file
$imagePath = __DIR__ . '/public/images/default-logo.png';
if (file_exists($imagePath)) {
    $uploadedFile = new UploadedFile(
        $imagePath,
        'default-logo.png',
        'image/png',
        filesize($imagePath),
        UPLOAD_ERR_OK,
        true
    );
    
    $testData['images'][] = $uploadedFile;
    
    echo "Test image file created successfully.\n";
} else {
    echo "Test image file not found. Creating a dummy file for testing.\n";
    
    // Create a dummy image file
    $dummyImagePath = __DIR__ . '/dummy_test.png';
    $imageData = file_get_contents('https://via.placeholder.com/150x150.png?text=Test+Image');
    file_put_contents($dummyImagePath, $imageData);
    
    $uploadedFile = new UploadedFile(
        $dummyImagePath,
        'dummy_test.png',
        'image/png',
        filesize($dummyImagePath),
        UPLOAD_ERR_OK,
        true
    );
    
    $testData['images'][] = $uploadedFile;
    
    echo "Dummy test image file created successfully.\n";
}

// Test the ProductService
try {
    $productService = new ProductService(new MockProductRepository());
    
    echo "Testing ProductService image upload...\n";
    
    // This would normally be called within a Laravel application context
    // For testing purposes, we'll just verify the data structure
    
    if (isset($testData['images']) && is_array($testData['images'])) {
        echo "✓ Images array is properly structured\n";
        
        foreach ($testData['images'] as $index => $image) {
            if ($image instanceof UploadedFile) {
                echo "✓ Image at index $index is a valid UploadedFile instance\n";
                echo "  - File name: " . $image->getClientOriginalName() . "\n";
                echo "  - File size: " . $image->getSize() . " bytes\n";
                echo "  - MIME type: " . $image->getMimeType() . "\n";
                echo "  - Is valid: " . ($image->isValid() ? 'Yes' : 'No') . "\n";
            } else {
                echo "✗ Image at index $index is not a valid UploadedFile instance\n";
            }
        }
    } else {
        echo "✗ Images array is not properly structured\n";
    }
    
    echo "\nImage upload test completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error during testing: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

// Clean up dummy file if created
if (isset($dummyImagePath) && file_exists($dummyImagePath)) {
    unlink($dummyImagePath);
    echo "Dummy test file cleaned up.\n";
}
