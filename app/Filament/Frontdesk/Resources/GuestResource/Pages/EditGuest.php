<?php

namespace App\Filament\Frontdesk\Resources\GuestResource\Pages;

use App\Filament\Frontdesk\Resources\GuestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGuest extends EditRecord
{
    protected static string $resource = GuestResource::class;
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
