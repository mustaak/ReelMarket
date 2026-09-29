<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        $grouped = Permission::all()->groupBy(function (Permission $permission) {
            return str($permission->name)->afterLast('_')->toString();
        });

        $sections = [];

        foreach ($grouped as $module => $modulePermissions) {
            $fieldName = "permissions_{$module}";

            $sections[] = Section::make(ucfirst($module).' Management')
                ->schema([
                    CheckboxList::make($fieldName)
                        ->label('')
                        ->options(
                            $modulePermissions->pluck('name', 'id')->mapWithKeys(
                                fn ($name, $id) => [$id => ucwords(str_replace('_', ' ', $name))]
                            )
                        )
                        ->columns(2)
                        ->bulkToggleable()
                        ->afterStateHydrated(function (CheckboxList $component, $record) use ($modulePermissions) {
                            if (! $record) {
                                return;
                            }

                            $ids = $record->permissions()
                                ->whereIn('permissions.id', $modulePermissions->pluck('id'))
                                ->pluck('permissions.id')
                                ->toArray();

                            $component->state($ids);
                        }),
                ])
                ->collapsible();
        }

        return $schema->components([
            TextInput::make('name')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            ...$sections,
        ]);
    }
}