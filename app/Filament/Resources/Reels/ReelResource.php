<?php

namespace App\Filament\Resources\Reels;

use App\Filament\Resources\Reels\Pages\CreateReel;
use App\Filament\Resources\Reels\Pages\EditReel;
use App\Filament\Resources\Reels\Pages\ListReels;
use App\Filament\Resources\Reels\Schemas\ReelForm;
use App\Filament\Resources\Reels\Tables\ReelsTable;
use App\Models\Reel;
use BackedEnum;
use App\Filament\Resources\BaseResource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ReelResource extends BaseResource
{
    protected static ?string $model = Reel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static UnitEnum|string|null $navigationGroup = 'Social network';
    

    public static function form(Schema $schema): Schema
    {
        return ReelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReelsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReels::route('/'),
            'create' => CreateReel::route('/create'),
            'edit' => EditReel::route('/{record}/edit'),
        ];
    }
}
