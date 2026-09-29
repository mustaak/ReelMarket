<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Post details')
                    ->description('Who posted it, what it says, and who can see it')
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
                        FileUpload::make('image')
                            ->label('Post Image')
                            ->image()
                            ->disk('public')
                            ->directory('posts')
                            ->imageEditor()
                            ->maxSize(5120)
                            ->nullable(),
                            
                        Textarea::make('content')
                            ->rows(4)
                            ->columnSpanFull(),
                        Select::make('visibility')
                            ->options([
                                'public' => 'Public',
                                'followers' => 'Followers only',
                            ])
                            ->required()
                            ->default('public'),
                    ])
                    ->columns(2),

                Section::make('Moderation')
                    ->description('Controls whether this post is visible to users')
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