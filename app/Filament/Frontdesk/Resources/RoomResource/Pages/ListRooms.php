<?php

namespace App\Filament\Frontdesk\Resources\RoomResource\Pages;

use App\Filament\Frontdesk\Resources\RoomResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRooms extends ListRecords
{
    protected static string $resource = RoomResource::class;

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        return [
            Actions\CreateAction::make()
            ->visible(condition: fn() => $user->role === 'FrontDesk'),

        ];
    }
}
