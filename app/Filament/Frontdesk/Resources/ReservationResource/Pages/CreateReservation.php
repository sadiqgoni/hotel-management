<?php

namespace App\Filament\Frontdesk\Resources\ReservationResource\Pages;

use App\Filament\Frontdesk\Resources\ReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateReservation extends CreateRecord
{
    protected static string $resource = ReservationResource::class;
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
}
