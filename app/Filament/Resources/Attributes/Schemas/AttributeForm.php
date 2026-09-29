<?php

namespace App\Filament\Resources\Attributes\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AttributeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Attribute Details')
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
                        ->unique(ignoreRecord: true),

                    Select::make('type')
                        ->options([
                            'select' => 'Select (text options)',
                            'color' => 'Color (swatch)',
                            'text' => 'Text',
                        ])
                        ->default('select')
                        ->required()
                        ->live(),
                ]),

            Section::make('Values')
                ->schema([
                    Repeater::make('values')
                        ->relationship('values')
                        ->schema([
                            TextInput::make('value')
                                ->required()
                                ->maxLength(255),

                            ColorPicker::make('color_code')
                                ->visible(fn (callable $get) => $get('../../type') === 'color'),
                        ])
                        ->columns(2)
                        ->columnSpanFull()
                        ->addActionLabel('Add Value')
                        ->reorderable(false)
                        ->defaultItems(0),
                ]),
        ]);
    }
}