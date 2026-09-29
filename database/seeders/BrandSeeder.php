<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    private array $colors = [
        [52, 73, 94],     // dark blue-grey
        [230, 126, 34],   // orange
        [26, 188, 156],   // turquoise
        [231, 76, 60],    // red
        [155, 89, 182],   // purple
        [46, 204, 113],   // green
    ];

    public function run(): void
    {
        $brands = [
            'Nike',
            'Samsung',
            'Apple',
            'Sony',
            'Adidas',
            'Puma',
        ];

        Storage::disk('public')->makeDirectory('brands');

        foreach ($brands as $index => $name) {
            $slug = Str::slug($name);
            $logo = $this->makePlaceholderLogo($name, $slug, $this->colors[$index % count($this->colors)]);

            Brand::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'logo' => $logo,
                    'description' => "Sample description for {$name}.",
                    'status' => true,
                ]
            );
        }
    }

    private function makePlaceholderLogo(string $label, string $slug, array $rgb): string
    {
        $relativePath = "brands/{$slug}.png";
        $fullPath = Storage::disk('public')->path($relativePath);

        if (file_exists($fullPath)) {
            return $relativePath;
        }

        $width = 300;
        $height = 300;

        $image = imagecreatetruecolor($width, $height);
        $bgColor = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
        $textColor = imagecolorallocate($image, 255, 255, 255);

        imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);

        $font = 5;
        $textWidth = imagefontwidth($font) * strlen($label);
        $textX = (int) (($width - $textWidth) / 2);
        $textY = (int) (($height - imagefontheight($font)) / 2);

        imagestring($image, $font, $textX, $textY, $label, $textColor);

        imagepng($image, $fullPath);
        imagedestroy($image);

        return $relativePath;
    }
}