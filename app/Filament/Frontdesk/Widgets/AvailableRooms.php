<?php

namespace App\Filament\Frontdesk\Widgets;

use App\Models\Room;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Table;

class AvailableRooms extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Room::query()->where('status', 'Available')
            )
            ->columns([
                TextColumn::make('room_number')->label('Room Number'),
                TextColumn::make('roomType.name')->label('Room Type'),
                TextColumn::make('price_per_night')->label('Price per Night')->money('NGN')
            ]);
    }
}
