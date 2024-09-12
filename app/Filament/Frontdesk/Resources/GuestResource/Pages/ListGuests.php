<?php

namespace App\Filament\Frontdesk\Resources\GuestResource\Pages;

use App\Filament\Frontdesk\Resources\GuestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGuests extends ListRecords
{
    protected static string $resource = GuestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
