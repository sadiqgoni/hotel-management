<?php
namespace App\Filament\Management\Pages;

use App\Traits\HasReportPageSidebar;
use App\Traits\HasTranslatableResource;
use Filament\Pages\Page;

class RestaurantReport extends Page
{
    use HasReportPageSidebar, HasTranslatableResource;

    protected static string $view = 'filament.management.pages.restaurant-report';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Restaurant Report';
    protected static ?string $navigationGroup = 'General Reports';

}
