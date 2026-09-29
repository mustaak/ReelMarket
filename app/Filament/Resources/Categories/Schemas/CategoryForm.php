<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Category Details')
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

                    Select::make('parent_id')
                        ->label('Parent Category')
                        ->options(function ($record) {
                            return Category::query()
                                ->when($record, fn ($q) => $q->whereKeyNot($record->id))
                                ->orderBy('name')
                                ->pluck('name', 'id');
                        })
                        ->searchable()
                        ->preload()
                        ->placeholder('None (top-level category)'),

                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    Toggle::make('status')
                        ->label('Active')
                        ->default(true)
                        ->inline(false),
                ]),

            Section::make('Description & Image')
                ->columns(1)
                ->schema([
                    Textarea::make('description')
                        ->rows(4)
                        ->maxLength(2000),

                    FileUpload::make('image')
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->directory('categories')
                        ->visibility('public')
                        ->maxSize(2048),
                ]),
        ]);
    }
}