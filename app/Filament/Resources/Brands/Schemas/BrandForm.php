<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Str;

class BrandForm
{
     public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Brand Details')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $state, callable $set, string $operation) {
                            if ($operation === 'create') {
                                $set('slug', Str::slug($state));
                            }
                        }),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->helperText('Auto-generated from name, editable.'),

                    Toggle::make('status')
                        ->label('Active')
                        ->default(true)
                        ->inline(false),
                ]),

            Section::make('Description & Logo')
                ->columns(1)
                ->schema([
                    Textarea::make('description')
                        ->rows(4)
                        ->maxLength(2000),

                    FileUpload::make('logo')
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->directory('brands')
                        ->visibility('public')
                        ->maxSize(2048),
                ]),
        ]);
    }
}
