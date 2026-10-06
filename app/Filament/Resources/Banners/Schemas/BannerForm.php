<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Banner Content')
                    ->description('Add the main content displayed on the banner.')
                    ->schema([
                        TextInput::make('tag')
                            ->label('Tag')
                            ->placeholder('Limited time offer')
                            ->maxLength(255),

                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->placeholder('Fresh picks,')
                            ->maxLength(255),

                        TextInput::make('accent')
                            ->label('Accent')
                            ->placeholder('best prices')
                            ->maxLength(255),

                        Textarea::make('sub')
                            ->label('Description')
                            ->placeholder('Discover products from top brands and the creators you follow.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Call To Action')
                    ->description('Configure the button shown on the banner.')
                    ->schema([
                        TextInput::make('cta_text')
                            ->label('Button Text')
                            ->placeholder('Shop now')
                            ->maxLength(255),

                        TextInput::make('cta_route')
                            ->label('Route Name')
                            ->placeholder('shop.index')
                            ->helperText('Example: shop.index or reels.index')
                            ->maxLength(255),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Banner Image')
                    ->description('Upload the image used for this banner.')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Banner Image')
                            ->image()
                            ->disk('public')
                            ->directory('banners')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Display Settings')
                    ->description('Control whether the banner is visible and its position.')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive banners will not appear on the homepage.'),

                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->default(0)
                            ->helperText('Lower numbers appear first.'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

            ]);
    }
}