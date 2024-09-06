<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Jobs\ExpireReservation;
use App\Models\Reservation;
use App\Models\Guest;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;
    protected static ?string $navigationGroup = 'Bookings & Guests Management';
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Manage Reservations';
    protected static ?string $modelLabel = 'Manage Reservations';
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema(static::getFormSchema());
    }
    public static function getFormSchema(): array
    {
        return [
            Card::make()
                ->schema([
                    Section::make('Reservation Details')
                        ->description('Provide details about the guest and room')

                        ->schema([
                            Select::make('guest_id')
                                ->label('Guest')
                                ->preload()
                                ->searchable()
                                ->options(Guest::query()->pluck('name', 'id')->toArray())
                                ->required()
                                ->placeholder('Select Guest'),

                                Select::make('room_id')
                                ->label('Room')
                                ->searchable()
                                ->options(function (callable $get) {
                                    $checkInDate = Carbon::parse($get('check_in_date'));
                                    $checkOutDate = Carbon::parse($get('check_out_date'));
                                    
                                    $occupiedRoomIds = Reservation::where(function ($query) use ($checkInDate, $checkOutDate) {
                                        $query->whereBetween('check_in_date', [$checkInDate, $checkOutDate])
                                              ->orWhereBetween('check_out_date', [$checkInDate, $checkOutDate])
                                              ->orWhereRaw('? BETWEEN check_in_date AND check_out_date', [$checkInDate])
                                              ->orWhereRaw('? BETWEEN check_in_date AND check_out_date', [$checkOutDate]);
                                    })->pluck('room_id')->toArray();
                            
                                    return Room::whereNotIn('id', $occupiedRoomIds)
                                        ->pluck('room_number', 'id');
                                })
                                ->required()
                                ->placeholder('Select Room')
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $room = Room::find($state);
                                    $set('price_per_night', $room?->price_per_night ?? 0);
                                }),                            
                            TextInput::make('price_per_night')
                                ->label('Price per Night')
                                ->placeholder('Auto-filled based on Room Type')
                                ->readOnly(),

                            DatePicker::make('check_in_date')
                                ->label('Check-In Date')
                                ->required()
                                ->placeholder('Select Check-In Date')
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    static::updateTotalAmount($get, $set);
                                }),

                            DatePicker::make('check_out_date')
                                ->label('Check-Out Date')
                                ->required()
                                ->afterOrEqual('check_in_date')
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    static::updateTotalAmount($get, $set);
                                }),

                            TextInput::make('total_amount')
                                ->label('Total Amount')
                                ->readOnly()
                                ->numeric()
                                ->placeholder('Auto-calculated based on Room Rate and Dates'),
                            Select::make('status')
                                ->label('Status')
                                ->searchable()
                                ->options([
                                    'Confirmed' => 'Confirmed',
                                    'On Hold' => 'On Hold',
                                ])
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    // Handle On Hold logic here
                                    if ($state === 'On Hold') {
                                        static::scheduleExpiration($get('reservation_id'));
                                        Notification::make()
                                            ->title('Reservation On Hold')
                                            ->body('This reservation is on hold and will expire in 1 hour.')
                                            ->success()
                                            ->send();

                                    }


                                }),

                            Textarea::make('special_requests')
                                ->label('Special Requests')
                                ->placeholder('Enter any special requests'),

                            TextInput::make('number_of_people')
                                ->label('Number of People')
                                ->numeric()
                                ->required(),
                        ])
                        ->collapsible()
                        ->columns(2),
                ]),
        ];
    }
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

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
                    })
                    ->sortable(),
                TextColumn::make('number_of_people')
                    ->label('Number of People')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    protected static function updateTotalAmount(callable $get, callable $set)
    {
        $checkInDate = Carbon::parse($get('check_in_date'));
        $checkOutDate = Carbon::parse($get('check_out_date'));

        if ($checkInDate && $checkOutDate) {
            $days = $checkInDate->diffInDays($checkOutDate);
            $pricePerNight = $get('price_per_night');
            $totalAmount = $days * $pricePerNight;

            $set('total_amount', $totalAmount);
        }
    }

    public static function scheduleExpiration($reservationId)
    {
        ExpireReservation::dispatch($reservationId)->delay(now()->addHours(1));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
            'view' => Pages\ViewReservation::route('/{record}'),
        ];
    }
}
