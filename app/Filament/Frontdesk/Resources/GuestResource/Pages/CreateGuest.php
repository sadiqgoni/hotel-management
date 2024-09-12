<?php

namespace App\Filament\Frontdesk\Resources\GuestResource\Pages;

use App\Filament\Frontdesk\Resources\GuestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGuest extends CreateRecord
{
    protected static string $resource = GuestResource::class;
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
}
