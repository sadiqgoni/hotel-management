<?php

namespace App\Filament\Frontdesk\Resources\RoomTypeResource\Pages;

use App\Filament\Frontdesk\Resources\RoomTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoomType extends EditRecord
{
    protected static string $resource = RoomTypeResource::class;
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
