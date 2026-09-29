<?php

namespace Database\Seeders;

use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    private array $colors = [
        [41, 128, 185], [39, 174, 96], [211, 84, 0],
        [142, 68, 173], [192, 57, 43], [22, 160, 133],
    ];

    public function run(): void
    {
        $products = [
            [
                'name' => 'Wireless Bluetooth Headphones',
                'category' => 'Mobiles',
                'brand' => 'Sony',
                'price' => 4999,
                'sale_price' => 3999,
                'stock' => 45,
                'attributes' => ['Black', 'White'],
            ],
            [
                'name' => 'Running Shoes',
                'category' => null,
                'brand' => 'Nike',
                'price' => 5499,
                'sale_price' => null,
                'stock' => 30,
                'attributes' => ['Red', 'Black', 'M', 'L', 'XL'],
            ],
            [
                'name' => 'Smart LED TV 43 inch',
                'category' => 'Electronics',
                'brand' => 'Samsung',
                'price' => 32999,
                'sale_price' => 28999,
                'stock' => 12,
                'attributes' => [],
            ],
            [
                'name' => 'Cotton Casual T-Shirt',
                'category' => 'Men',
                'brand' => 'Puma',
                'price' => 899,
                'sale_price' => 649,
                'stock' => 100,
                'attributes' => ['Blue', 'White', 'S', 'M', 'L', 'Cotton'],
            ],
            [
                'name' => 'Gaming Laptop 15.6"',
                'category' => 'Laptops',
                'brand' => 'Apple',
                'price' => 89999,
                'sale_price' => null,
                'stock' => 5,
                'attributes' => [],
            ],
            [
                'name' => 'Yoga Mat',
                'category' => 'Fitness Equipment',
                'brand' => null,
                'price' => 1299,
                'sale_price' => 999,
                'stock' => 0,
                'attributes' => ['Green'],
            ],
        ];

        Storage::disk('public')->makeDirectory('products');

        foreach ($products as $index => $data) {
            $slug = Str::slug($data['name']);

            $product = Product::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'sku' => 'SKU-' . strtoupper(Str::random(8)),
                    'short_description' => "High quality {$data['name']}.",
                    'description' => "<p>This is a detailed description for {$data['name']}. Great quality and value for money.</p>",
                    'price' => $data['price'],
                    'sale_price' => $data['sale_price'],
                    'cost_price' => $data['price'] * 0.6,
                    'stock_quantity' => $data['stock'],
                    'status' => 'active',
                    'visibility' => 'visible',
                    'featured' => $index < 2,
                    'category_id' => $data['category'] ? Category::where('name', $data['category'])->value('id') : null,
                    'brand_id' => $data['brand'] ? Brand::where('name', $data['brand'])->value('id') : null,
                    'weight' => rand(1, 20) / 10,
                ]
            );

            // Attach attribute values
            if (! empty($data['attributes'])) {
                $valueIds = AttributeValue::whereIn('value', $data['attributes'])->pluck('id');
                $product->attributeValues()->sync($valueIds);
            }

            // Generate 2 placeholder images per product
            if ($product->images()->count() === 0) {
                for ($i = 1; $i <= 2; $i++) {
                    $imagePath = $this->makePlaceholderImage(
                        $data['name'] . " #{$i}",
                        $slug . "-{$i}",
                        $this->colors[($index + $i) % count($this->colors)]
                    );

                    $product->images()->create([
                        'image' => $imagePath,
                        'sort_order' => $i,
                    ]);
                }
            }
        }
    }

    private function makePlaceholderImage(string $label, string $slug, array $rgb): string
    {
        $relativePath = "products/{$slug}.png";
        $fullPath = Storage::disk('public')->path($relativePath);

        if (file_exists($fullPath)) {
            return $relativePath;
        }

        $width = 400;
        $height = 400;

        $image = imagecreatetruecolor($width, $height);
        $bgColor = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
        $textColor = imagecolorallocate($image, 255, 255, 255);

        imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);

        $font = 4;
        $lines = explode(' ', $label);
        $y = ($height / 2) - 20;

        foreach (array_chunk($lines, 2) as $chunk) {
            $line = implode(' ', $chunk);
            $textWidth = imagefontwidth($font) * strlen($line);
            $x = (int) (($width - $textWidth) / 2);
            imagestring($image, $font, $x, (int) $y, $line, $textColor);
            $y += 20;
        }

        imagepng($image, $fullPath);
        imagedestroy($image);

        return $relativePath;
    }
}