<?php

namespace App\Filament\Frontdesk\Resources\GroupReservationResource\Pages;

use App\Filament\Frontdesk\Resources\GroupReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Frontdesk\Resources\RoomTypeResource;

class CreateGroupReservation extends CreateRecord
{
    protected static string $resource = GroupReservationResource::class;
}
