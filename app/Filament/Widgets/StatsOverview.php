<?php

namespace App\Filament\Widgets;

use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Active Users', User::where('status', true)->count())
                ->description('Currently active')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Inactive Users', User::where('status', false)->count())
                ->description('Deactivated accounts')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Total Roles', Role::count())
                ->description('Configured roles')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('warning'),

            Stat::make('Total Permissions', Permission::count())
                ->description('Granular permissions')
                ->descriptionIcon('heroicon-m-key')
                ->color('gray'),

            Stat::make('Total Categories', Category::count())
                ->description('Product categories')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('info'),

            Stat::make('Total Brands', Brand::count())
                ->description('Registered brands')
                ->descriptionIcon('heroicon-m-tag')
                ->color('info'),

            Stat::make('Total Attributes', Attribute::count())
                ->description('Product attributes')
                ->descriptionIcon('heroicon-m-adjustments-horizontal')
                ->color('info'),

            Stat::make('Total Products', Product::count())
                ->description('All products')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success'),

            Stat::make('Active Products', Product::where('status', 'active')->count())
                ->description('Currently active')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Out of Stock', Product::where('stock_quantity', 0)->count())
                ->description('Needs restocking')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }

    protected function getColumns(): int
    {
        return 4; // ya jo bhi aapko sahi lage (default 3 hota hai)
    }
}