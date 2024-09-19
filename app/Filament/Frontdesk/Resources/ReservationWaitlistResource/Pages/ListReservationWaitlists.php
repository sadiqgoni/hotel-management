<?php
namespace App\Filament\Frontdesk\Resources\ReservationWaitlistResource\Pages;

use App\Filament\Frontdesk\Resources\ReservationWaitlistResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReservationWaitlists extends ListRecords
{
    protected static string $resource = ReservationWaitlistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
