<?php

namespace App\Filament\Resources\Reels\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Reel details')
                    ->description('Video, caption, and optional linked product')
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        FileUpload::make('video_path')
                            ->label('Video')
                            ->disk('public')
                            ->directory('reels')
                            ->acceptedFileTypes(['video/mp4', 'video/quicktime'])
                            ->required(),
                        FileUpload::make('thumbnail')
                            ->image()
                            ->disk('public')
                            ->directory('reels/thumbnails'),
                        Textarea::make('caption')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Moderation')
                    ->description('Controls whether this reel is visible to users')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'published' => 'Published',
                                'flagged' => 'Flagged',
                                'removed' => 'Removed',
                            ])
                            ->required()
                            ->default('published'),
                    ]),
            ]);
    }
}