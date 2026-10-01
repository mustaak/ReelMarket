<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Product')
                ->columnSpanFull()
                ->tabs([
                    Tab::make('General')
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (string $state, callable $set, string $operation) {
                                    if ($operation === 'create') {
                                        $set('slug', Str::slug($state));
                                    }
                                })
                                ->columnSpanFull(),

                            TextInput::make('slug')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),

                            TextInput::make('sku')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->label('SKU'),

                            Select::make('category_id')
                                ->label('Category')
                                ->options(fn () => Category::orderBy('name')->pluck('name', 'id'))
                                ->searchable()
                                ->preload(),

                            Select::make('brand_id')
                                ->label('Brand')
                                ->options(fn () => Brand::orderBy('name')->pluck('name', 'id'))
                                ->searchable()
                                ->preload(),

                            Textarea::make('short_description')
                                ->rows(2)
                                ->maxLength(500)
                                ->columnSpanFull(),

                            RichEditor::make('description')
                                ->columnSpanFull(),
                        ])
                        ->columns(2),

                    Tab::make('Pricing & Stock')
                        ->schema([
                            TextInput::make('price')
                                ->required()
                                ->numeric()
                                ->prefix('₹'),

                            TextInput::make('sale_price')
                                ->numeric()
                                ->prefix('₹')
                                ->helperText('Optional — leave blank if no discount'),

                            TextInput::make('cost_price')
                                ->numeric()
                                ->prefix('₹')
                                ->helperText('Internal cost, not shown to customers'),

                            TextInput::make('stock_quantity')
                                ->required()
                                ->numeric()
                                ->default(0),

                            TextInput::make('weight')
                                ->numeric()
                                ->suffix('kg'),

                            Select::make('status')
                                ->options([
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                    'draft' => 'Draft',
                                ])
                                ->default('active')
                                ->required(),

                            Select::make('visibility')
                                ->options([
                                    'visible' => 'Visible',
                                    'hidden' => 'Hidden',
                                ])
                                ->default('visible')
                                ->required(),

                            Toggle::make('featured')
                                ->label('Featured Product')
                                ->inline(false),
                        ])
                        ->columns(2),

                    Tab::make('Images')
                        ->schema([
                            Repeater::make('images')
                                ->relationship('images')
                                ->schema([
                                    FileUpload::make('image')
                                        ->image()
                                        ->imageEditor()
                                        ->disk('public')
                                        ->directory('products')
                                        ->visibility('public')
                                        ->maxSize(2048)
                                        ->required(),

                                    TextInput::make('sort_order')
                                        ->numeric()
                                        ->default(0),
                                ])
                                ->columns(2)
                                ->columnSpanFull()
                                ->addActionLabel('Add Image')
                                ->reorderable()
                                ->defaultItems(0),
                        ]),

                    Tab::make('Attributes')
                        ->schema([
                            CheckboxList::make('attributeValues')
                                ->relationship('attributeValues', 'value')
                                ->options(function () {
                                    return AttributeValue::with('attribute')
                                        ->get()
                                        ->groupBy('attribute.name')
                                        ->flatMap(fn ($values, $attrName) => $values->mapWithKeys(
                                            fn ($v) => [$v->id => "{$attrName}: {$v->value}"]
                                        ));
                                })
                                ->columns(3)
                                ->searchable(),
                        ]),

                    Tab::make('Variants')
                        ->schema([
                            Repeater::make('variants')
                                ->relationship()
                                ->schema([
                                    TextInput::make('label')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('sku')
                                        ->required()
                                        ->maxLength(255),

                                    KeyValue::make('options')
                                        ->keyLabel('Option')
                                        ->valueLabel('Value')
                                        ->required(),

                                    TextInput::make('price')
                                        ->numeric()
                                        ->minValue(0)
                                        ->prefix('₹')
                                        ->helperText('Optional — uses the product price when blank'),

                                    TextInput::make('stock_quantity')
                                        ->required()
                                        ->integer()
                                        ->minValue(0)
                                        ->default(0),
                                ])
                                ->columns(2)
                                ->columnSpanFull()
                                ->addActionLabel('Add variant')
                                ->defaultItems(0),
                        ]),
                ]),
        ]);
    }
}
