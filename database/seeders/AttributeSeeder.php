<?php

namespace Database\Seeders;

use App\Models\Attribute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'Color',
                'type' => 'color',
                'values' => [
                    ['value' => 'Red', 'color_code' => '#E74C3C'],
                    ['value' => 'Blue', 'color_code' => '#3498DB'],
                    ['value' => 'Black', 'color_code' => '#2C3E50'],
                    ['value' => 'White', 'color_code' => '#FFFFFF'],
                    ['value' => 'Green', 'color_code' => '#2ECC71'],
                ],
            ],
            [
                'name' => 'Size',
                'type' => 'select',
                'values' => [
                    ['value' => 'S', 'color_code' => null],
                    ['value' => 'M', 'color_code' => null],
                    ['value' => 'L', 'color_code' => null],
                    ['value' => 'XL', 'color_code' => null],
                    ['value' => 'XXL', 'color_code' => null],
                ],
            ],
            [
                'name' => 'Material',
                'type' => 'text',
                'values' => [
                    ['value' => 'Cotton', 'color_code' => null],
                    ['value' => 'Polyester', 'color_code' => null],
                    ['value' => 'Leather', 'color_code' => null],
                ],
            ],
        ];

        foreach ($data as $attributeData) {
            $attribute = Attribute::firstOrCreate(
                ['slug' => Str::slug($attributeData['name'])],
                [
                    'name' => $attributeData['name'],
                    'type' => $attributeData['type'],
                ]
            );

            foreach ($attributeData['values'] as $valueData) {
                $attribute->values()->firstOrCreate(
                    ['value' => $valueData['value']],
                    ['color_code' => $valueData['color_code']]
                );
            }
        }
    }
}