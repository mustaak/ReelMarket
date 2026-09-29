<?php

namespace App\Services;

use Illuminate\Support\Str;

class FilamentPermissionService
{
    public static function moduleFromResource(string $resource): string
    {
        $class = class_basename($resource);

        $class = Str::beforeLast($class, 'Resource');

        return Str::plural(Str::snake($class));
    }

    public static function can(
        string $resource,
        string $action
    ): bool {
        $module = static::moduleFromResource($resource);

        return auth()->check()
            && auth()->user()->can("{$action}_{$module}");
    }
}