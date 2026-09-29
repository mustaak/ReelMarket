<?php

namespace App\Filament\Resources\Reports\Schemas;

use App\Models\Post;
use App\Models\Reel;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Report details')
                    ->schema([
                        MorphToSelect::make('reportable')
                            ->label('Content being reported')
                            ->types([
                                MorphToSelect\Type::make(Post::class)
                                    ->titleColumnName('content'),
                                MorphToSelect\Type::make(Reel::class)
                                    ->titleColumnName('caption'),
                            ])
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        Select::make('reported_by')
                            ->relationship('reportedBy', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('reason')
                            ->options([
                                'spam' => 'Spam',
                                'abuse' => 'Abusive content',
                                'nudity' => 'Nudity / sexual content',
                                'harassment' => 'Harassment',
                                'other' => 'Other',
                            ])
                            ->required(),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Admin action')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'reviewed' => 'Reviewed (no action)',
                                'actioned' => 'Actioned (content removed)',
                            ])
                            ->required()
                            ->default('pending'),
                        Textarea::make('admin_note')
                            ->label('Internal note')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}