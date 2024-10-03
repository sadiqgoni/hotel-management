<?php

namespace App\Traits;

use App\Filament\Management\Pages\OrderReport;
// use App\Filament\Tenant\Pages\ProductReport;
// use App\Filament\Tenant\Pages\PurchasingReport;
// use App\Filament\Tenant\Pages\SellingReport;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;
use AymanAlhattami\FilamentPageWithSidebar\PageNavigationItem;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

trait HasReportPageSidebar
{
    use HasPageSidebar;

    public static function sidebar(): FilamentPageSidebar
    {
        return FilamentPageSidebar::make()
            ->topbarNavigation()
            ->setNavigationItems([
                static::generateNavigationItem(OrderReport::class),
                // static::generateNavigationItem(ProductReport::class),
               
            ]);
    }

    private static function generateNavigationItem(string $resource, ?string $feature = null): PageNavigationItem
    {
    
        $active = false;
    
        // Check if the resource is a Page and has a route
        if ((new $resource) instanceof Page) {
            try {
                $active = Str::of($resource::getRouteName())->exactly(Route::current()->getName());
            } catch (\Symfony\Component\Routing\Exception\RouteNotFoundException $e) {
                // Handle missing route gracefully
                $active = false;
            }
        }
    
        if ((new $resource) instanceof Resource) {
            $active = Str::of(Route::currentRouteName())->contains($resource::getRouteBaseName());
        }
    
        return PageNavigationItem::make($resource::getLabel())
            ->icon($resource::getNavigationIcon())
            ->isActiveWhen(fn (): bool => $active)
            ->url(fn (): string => $resource::getUrl());
    }
    
}
