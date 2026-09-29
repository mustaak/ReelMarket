<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    private array $colors = [
        [41, 128, 185],   // blue
        [39, 174, 96],    // green
        [211, 84, 0],     // orange
        [142, 68, 173],   // purple
        [192, 57, 43],    // red
        [22, 160, 133],   // teal
    ];

    public function run(): void
    {
        $data = [
            'Electronics' => ['Mobiles', 'Laptops', 'Accessories'],
            'Fashion' => ['Men', 'Women', 'Kids'],
            'Home & Living' => ['Furniture', 'Kitchen', 'Decor'],
            'Sports & Outdoors' => ['Fitness Equipment', 'Cycling', 'Camping'],
        ];

        Storage::disk('public')->makeDirectory('categories');

        $sort = 1;
        $colorIndex = 0;

        foreach ($data as $parentName => $children) {
            $parentSlug = Str::slug($parentName);
            $parentImage = $this->makePlaceholderImage($parentName, $parentSlug, $this->colors[$colorIndex++ % count($this->colors)]);

            $parent = Category::firstOrCreate(
                ['slug' => $parentSlug],
                [
                    'name' => $parentName,
                    'parent_id' => null,
                    'status' => true,
                    'sort_order' => $sort++,
                    'image' => $parentImage,
                ]
            );

            $childSort = 1;

            foreach ($children as $childName) {
                $childSlug = Str::slug($parentName . '-' . $childName);
                $childImage = $this->makePlaceholderImage($childName, $childSlug, $this->colors[$colorIndex++ % count($this->colors)]);

                Category::firstOrCreate(
                    ['slug' => $childSlug],
                    [
                        'name' => $childName,
                        'parent_id' => $parent->id,
                        'status' => true,
                        'sort_order' => $childSort++,
                        'image' => $childImage,
                    ]
                );
            }
        }
    }

    /**
     * Generates a simple solid-color PNG with the category name as text,
     * saves it to storage/app/public/categories/, and returns the relative path.
     */
    private function makePlaceholderImage(string $label, string $slug, array $rgb): string
    {
        $relativePath = "categories/{$slug}.png";
        $fullPath = Storage::disk('public')->path($relativePath);

        // Skip regenerating if it already exists
        if (file_exists($fullPath)) {
            return $relativePath;
        }

        $width = 400;
        $height = 300;

        $image = imagecreatetruecolor($width, $height);
        $bgColor = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
        $textColor = imagecolorallocate($image, 255, 255, 255);

        imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);

        $font = 5; // built-in GD font
        $textWidth = imagefontwidth($font) * strlen($label);
        $textX = (int) (($width - $textWidth) / 2);
        $textY = (int) (($height - imagefontheight($font)) / 2);

        imagestring($image, $font, $textX, $textY, $label, $textColor);

        imagepng($image, $fullPath);
        imagedestroy($image);

        return $relativePath;
    }
}