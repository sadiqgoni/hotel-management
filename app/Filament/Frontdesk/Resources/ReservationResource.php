<?php

namespace App\Filament\Frontdesk\Resources;

use App\Filament\Frontdesk\Resources\ReservationResource\Pages;
use App\Filament\Frontdesk\Resources\ReservationResource\RelationManagers;
use App\Jobs\ExpireReservation;
use App\Models\Reservation;
use App\Models\Guest;
use App\Models\Room;
use Filament\Forms\Components\Actions\Action;
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
        return $form
            ->schema([
                Card::make()->schema([
                    Section::make('Reservation Details')
                        ->description('Add guest and reservation details')
                        ->schema([

                            Select::make('guest_id')
                                ->label('Guest')
                                ->preload()
                                ->searchable()
                                ->options(Guest::pluck('name', 'id')->toArray())
                                ->required()
                                ->reactive()
                                ->createOptionForm([
                                    // Fields for creating a new guest
                                    TextInput::make('name')
                                        ->label('Full Name')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('phone_number')
                                        ->label('Phone Number')
                                        ->unique(Guest::class, 'phone_number')
                                        ->maxLength(255),

                                    TextInput::make('nin_number')
                                        ->label('NIN Number')
                                        ->unique(Guest::class, 'nin_number')
                                        ->maxLength(255),

                                    Textarea::make('preferences')
                                        ->label('Preferences')
                                        ->placeholder('Enter preferences (e.g., Halal food, quiet room)')
                                ])
                                ->createOptionAction(function (Action $action) {
                                    return $action
                                        ->modalHeading('Create Guest')
                                        ->modalButton('Create Guest')
                                        ->modalWidth('lg');
                                })
                                ->createOptionUsing(function ($data) {
                                    // Logic for creating a new guest
                                    $guest = Guest::create([
                                        'name' => $data['name'],
                                        'phone_number' => $data['phone_number'],
                                        'nin_number' => $data['nin_number'],
                                        'preferences' => $data['preferences'] ?? null,
                                    ]);

                                    return $guest->id;  // Return the newly created guest ID
                                }),
                            Select::make('room_id')
                                ->label('Room')
                                ->searchable()
                                ->options(Room::all()->pluck('room_number', 'id')->toArray())
                                ->required()
                                ->placeholder('Select Room')
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $room = Room::find($state);
                                    $set('price_per_night', $room?->price_per_night ?? 0);
                                }),

                            TextInput::make('price_per_night')
                                ->label('Price per Night')
                                ->readOnly(),

                            DatePicker::make('check_in_date')
                                ->label('Check-In Date')
                                ->required()
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
                                ->options([
                                    'Confirmed' => 'Confirmed',
                                    'On Hold' => 'On Hold',
                                ])
                                ->required(),

                            Textarea::make('special_requests')
                                ->label('Special Requests'),

                            TextInput::make('number_of_people')
                                ->label('Number of People')
                                ->required(),
                        ])
                        ->columns(2)
                ])
            ]);
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
                        'Checked In' => 'success',
                        'Checked Out' => 'warning',
                    })
                    ->sortable(),

            ])
            ->actions([
                Tables\Actions\Action::make('Print Reservation Slip')
                    ->icon('heroicon-o-printer')
                    ->action(function ($record) {
                        return redirect()->route('reservations.print', ['reservation' => $record->id]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Print Reservation Slip'),
                Tables\Actions\EditAction::make()
                ->color('warning'),
                Tables\Actions\ViewAction::make()
                ->color('success')
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
        ];
    }
}

