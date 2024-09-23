<?php

namespace App\Filament\Management\Resources\MenuItemResource\Pages;

use App\Filament\Management\Resources\MenuItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMenuItem extends CreateRecord
{
    protected static string $resource = MenuItemResource::class;
}
