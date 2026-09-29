<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected array $permissionIds = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->permissionIds = $this->extractPermissionIds($data);

        return $this->stripPermissionKeys($data);
    }

    protected function afterCreate(): void
    {
        $this->record->syncPermissions($this->permissionIds);
    }

    protected function extractPermissionIds(array $data): array
    {
        $ids = [];

        foreach ($data as $key => $value) {
            if (Str::startsWith($key, 'permissions_') && is_array($value)) {
                $ids = array_merge($ids, $value);
            }
        }

        return array_values(array_unique($ids));
    }

    protected function stripPermissionKeys(array $data): array
    {
        foreach (array_keys($data) as $key) {
            if (Str::startsWith($key, 'permissions_')) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}