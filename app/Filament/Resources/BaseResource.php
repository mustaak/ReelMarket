<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Illuminate\Support\Str;

abstract class BaseResource extends Resource
{
    /**
     * Get resource/module name automatically.
     *
     * ProductResource     -> products
     * CategoryResource    -> categories
     * BrandResource       -> brands
     * UserResource        -> users
     */
    public static function getPermissionModule(): string
    {
        $class = class_basename(static::class);

        $class = Str::beforeLast($class, 'Resource');

        return Str::plural(Str::snake($class));
    }

    /**
     * Generate permission name automatically.
     *
     * view   -> view_products
     * create -> create_products
     * edit   -> edit_products
     * delete -> delete_products
     */
    public static function getPermissionName(string $action): string
    {
        return "{$action}_" . static::getPermissionModule();
    }

    /**
     * View module / navigation.
     */
    public static function canViewAny(): bool
    {
        return auth()->user()?->can(
            static::getPermissionName('view')
        ) ?? false;
    }

    /**
     * Create record.
     */
    public static function canCreate(): bool
    {
        return auth()->user()?->can(
            static::getPermissionName('create')
        ) ?? false;
    }

    /**
     * Edit record.
     */
    public static function canEdit($record): bool
    {
        return auth()->user()?->can(
            static::getPermissionName('edit')
        ) ?? false;
    }

    /**
     * Delete single record.
     */
    public static function canDelete($record): bool
    {
        return auth()->user()?->can(
            static::getPermissionName('delete')
        ) ?? false;
    }

    /**
     * Bulk delete.
     */
    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can(
            static::getPermissionName('delete')
        ) ?? false;
    }
}