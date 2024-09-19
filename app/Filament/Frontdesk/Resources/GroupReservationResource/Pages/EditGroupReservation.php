<?php
namespace App\Filament\Frontdesk\Resources\GroupReservationResource\Pages;

use App\Filament\Frontdesk\Resources\GroupReservationResource;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGroupReservation extends EditRecord
{
    protected static string $resource = GroupReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
