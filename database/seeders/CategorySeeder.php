<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Constants\CategoryStatus;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing categories
        Category::query()->delete();

        // Create main categories (parent_id = null)
        $electronics = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Electronic devices and gadgets',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => null,
        ]);

        $clothing = Category::create([
            'name' => 'Clothing',
            'slug' => 'clothing',
            'description' => 'Fashion and apparel',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => null,
        ]);

        $books = Category::create([
            'name' => 'Books',
            'slug' => 'books',
            'description' => 'Books and literature',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => null,
        ]);

        $home = Category::create([
            'name' => 'Home & Garden',
            'slug' => 'home-garden',
            'description' => 'Home improvement and garden supplies',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => null,
        ]);

        $sports = Category::create([
            'name' => 'Sports & Outdoors',
            'slug' => 'sports-outdoors',
            'description' => 'Sports equipment and outdoor gear',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => null,
        ]);

        // Create subcategories for Electronics
        Category::create([
            'name' => 'Computers & Laptops',
            'slug' => 'computers-laptops',
            'description' => 'Desktop computers, laptops, and accessories',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => $electronics->id,
        ]);

        Category::create([
            'name' => 'Smartphones',
            'slug' => 'smartphones',
            'description' => 'Mobile phones and accessories',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => $electronics->id,
        ]);

        Category::create([
            'name' => 'Audio & Video',
            'slug' => 'audio-video',
            'description' => 'Audio equipment, TVs, and video devices',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => $electronics->id,
        ]);

        Category::create([
            'name' => 'Gaming',
            'slug' => 'gaming',
            'description' => 'Video games, consoles, and gaming accessories',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => $electronics->id,
        ]);

        // Create subcategories for Clothing
        Category::create([
            'name' => 'Men\'s Clothing',
            'slug' => 'mens-clothing',
            'description' => 'Clothing and apparel for men',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => $clothing->id,
        ]);

        Category::create([
            'name' => 'Women\'s Clothing',
            'slug' => 'womens-clothing',
            'description' => 'Clothing and apparel for women',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => $clothing->id,
        ]);

        Category::create([
            'name' => 'Kids\' Clothing',
            'slug' => 'kids-clothing',
            'description' => 'Clothing and apparel for children',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => $clothing->id,
        ]);

        Category::create([
            'name' => 'Shoes & Accessories',
            'slug' => 'shoes-accessories',
            'description' => 'Footwear and fashion accessories',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => $clothing->id,
        ]);

        // Create subcategories for Books
        Category::create([
            'name' => 'Fiction',
            'slug' => 'fiction',
            'description' => 'Fiction books and novels',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => $books->id,
        ]);

        Category::create([
            'name' => 'Non-Fiction',
            'slug' => 'non-fiction',
            'description' => 'Non-fiction books and educational materials',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => $books->id,
        ]);

        Category::create([
            'name' => 'Educational',
            'slug' => 'educational',
            'description' => 'Textbooks and educational books',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => $books->id,
        ]);

        Category::create([
            'name' => 'Children\'s Books',
            'slug' => 'childrens-books',
            'description' => 'Books for children and young readers',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => $books->id,
        ]);

        // Create subcategories for Home & Garden
        Category::create([
            'name' => 'Furniture',
            'slug' => 'furniture',
            'description' => 'Home furniture and decor',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => $home->id,
        ]);

        Category::create([
            'name' => 'Kitchen & Dining',
            'slug' => 'kitchen-dining',
            'description' => 'Kitchen appliances and dining essentials',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => $home->id,
        ]);

        Category::create([
            'name' => 'Garden & Outdoor',
            'slug' => 'garden-outdoor',
            'description' => 'Garden tools and outdoor equipment',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => $home->id,
        ]);

        Category::create([
            'name' => 'Home Improvement',
            'slug' => 'home-improvement',
            'description' => 'Tools and materials for home improvement',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => $home->id,
        ]);

        // Create subcategories for Sports & Outdoors
        Category::create([
            'name' => 'Fitness Equipment',
            'slug' => 'fitness-equipment',
            'description' => 'Exercise and fitness equipment',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => $sports->id,
        ]);

        Category::create([
            'name' => 'Outdoor Recreation',
            'slug' => 'outdoor-recreation',
            'description' => 'Camping, hiking, and outdoor gear',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => $sports->id,
        ]);

        Category::create([
            'name' => 'Team Sports',
            'slug' => 'team-sports',
            'description' => 'Equipment for team sports',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => $sports->id,
        ]);

        Category::create([
            'name' => 'Water Sports',
            'slug' => 'water-sports',
            'description' => 'Equipment for water sports and activities',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => $sports->id,
        ]);

        // Create some third-level categories (sub-subcategories)
        // Under Smartphones
        Category::create([
            'name' => 'iPhone',
            'slug' => 'iphone',
            'description' => 'Apple iPhone and accessories',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => Category::where('slug', 'smartphones')->first()->id,
        ]);

        Category::create([
            'name' => 'Android Phones',
            'slug' => 'android-phones',
            'description' => 'Android smartphones and accessories',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => Category::where('slug', 'smartphones')->first()->id,
        ]);

        // Under Gaming
        Category::create([
            'name' => 'Video Games',
            'slug' => 'video-games',
            'description' => 'Video games for various platforms',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => Category::where('slug', 'gaming')->first()->id,
        ]);

        Category::create([
            'name' => 'Gaming Consoles',
            'slug' => 'gaming-consoles',
            'description' => 'Gaming consoles and accessories',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => Category::where('slug', 'gaming')->first()->id,
        ]);

        // Under Fiction
        Category::create([
            'name' => 'Mystery & Thriller',
            'slug' => 'mystery-thriller',
            'description' => 'Mystery and thriller novels',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => Category::where('slug', 'fiction')->first()->id,
        ]);

        Category::create([
            'name' => 'Science Fiction',
            'slug' => 'science-fiction',
            'description' => 'Science fiction books and novels',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => Category::where('slug', 'fiction')->first()->id,
        ]);

        // Under Fitness Equipment
        Category::create([
            'name' => 'Cardio Equipment',
            'slug' => 'cardio-equipment',
            'description' => 'Cardio exercise equipment',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => Category::where('slug', 'fitness-equipment')->first()->id,
        ]);

        Category::create([
            'name' => 'Strength Training',
            'slug' => 'strength-training',
            'description' => 'Strength training equipment and weights',
            'status' => CategoryStatus::ACTIVE,
            'parent_id' => Category::where('slug', 'fitness-equipment')->first()->id,
        ]);

        $this->command->info('Categories seeded successfully!');
    }
}
