<?php

namespace App\Filament\Frontdesk\Widgets;

use App\Models\Reservation;
use App\Models\Room;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Table;

class LatestConfirmedReservation extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Confirmed Reservation';


    public function table(Table $table): Table
    {
        
        return $table
            ->query(Reservation::query()->where('status', 'Confirmed')->latest())
            ->columns([

                TextColumn::make('guest.name')
                    ->label('Guest Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('room.room_number')
                    ->label('Room Number')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('check_in_date')
                    ->label('Check-In Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('check_out_date')
                    ->label('Check-Out Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->sortable()
                    ->money('NGN'),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->color(fn(string $state): string => match ($state) {
                        'Confirmed' => 'info',
                        'On Hold' => 'danger',
                        'Checked In' => 'success',
                        'Checked Out' => 'warning',
                    })
                    ->sortable(),
            ]);
    }
}
