<?php

namespace App\Filament\Frontdesk\Resources;

use App\Filament\Frontdesk\Resources\CheckInCheckOutResource\Pages;
use App\Filament\Frontdesk\Resources\CheckInCheckOutResource\RelationManagers;
use App\Models\CheckInCheckOut;
use App\Models\Reservation;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use App\Models\Room;

// class CheckInCheckOutResource extends Resource
// {
//     protected static ?string $model = CheckInCheckOut::class;
//     protected static ?string $navigationIcon = 'heroicon-o-arrow-left-end-on-rectangle';
//     protected static ?string $navigationGroup = 'Guest Management';
//     protected static ?string $navigationLabel = 'Check-In/Check-Out';

//     protected static ?int $navigationSort = 2;

//     protected static ?int $headerTitle ='d';
//     // FORM SCHEMA
//     public static function form(Form $form): Form
//     {
//         return $form
//             ->schema([
//                 // Select only confirmed reservations that have not already been checked in or checked out
//                 Select::make('reservation_id')
//                     ->label('Reservation')
//                     ->preload()
//                     ->searchable()
//                     ->options(
//                         Reservation::query()
//                             ->where('status', 'Confirmed')  // Only show confirmed reservations
//                             ->whereNotExists(function ($query) {
//                                 $query->select('id')
//                                     ->from('check_in_check_outs')
//                                     ->whereColumn('reservation_id', 'reservations.id');
//                             })
//                             ->join('guests', 'reservations.guest_id', '=', 'guests.id')
//                             ->selectRaw("reservations.id, CONCAT(guests.name, ' - Reservation #', reservations.id) as label")
//                             ->pluck('label', 'reservations.id')
//                             ->toArray()
//                     )
//                     ->required(),

//                 DateTimePicker::make('check_in_time')
//                     ->label('Check-In Time')

//                     ->required(),

//                 DateTimePicker::make('check_out_time')
//                     ->label('Check-Out Time')
//                     ->nullable(),

//                 Select::make('status')
//                     ->label('Status')
//                     ->options([
//                         'Pending' => 'Pending',
//                         'Checked In' => 'Checked In',
//                         'Checked Out' => 'Checked Out',
//                     ])
//                     ->default('Pending')
//                     ->required(),
//             ]);
//     }

//     // TABLE SCHEMA
//     public static function table(Table $table): Table
//     {
//         return $table
//             ->columns([
//                 TextColumn::make('id')
//                     ->label('ID')
//                     ->sortable()
//                     ->searchable(),

//                 TextColumn::make('reservation.guest.name')
//                     ->label('Guest Name')
//                     ->sortable()
//                     ->searchable(),

//                 TextColumn::make('check_in_time')
//                     ->label('Check-In Time')
//                     ->sortable(),

//                 TextColumn::make('check_out_time')
//                     ->label('Check-Out Time')
//                     ->sortable(),

//                 BadgeColumn::make('status')
//                     ->label('Status')
//                     ->color(fn(string $state): string => match ($state) {
//                         'Pending' => 'danger',
//                         'Checked In' => 'success',
//                         'Checked Out' => 'warning',
//                     })
//                     ->sortable(),
//             ])
//             ->filters([
//                 Tables\Filters\SelectFilter::make('status')
//                     ->options([
//                         'Pending' => 'Pending',
//                         'Checked In' => 'Checked In',
//                         'Checked Out' => 'Checked Out',
//                     ]),
//             ])
//             ->actions([
//                 Tables\Actions\EditAction::make(),

//                 Tables\Actions\Action::make('Check In')
//                     ->icon('heroicon-o-arrow-left-end-on-rectangle')
//                     ->action(function (CheckInCheckOut $record) {
//                         if ($record->status === 'Checked In') {
//                             Notification::make()->title('Already Checked In!')->warning()->send();
//                             return;
//                         }

//                         // Ensure the reservation is confirmed and has not already been checked in or out
//                         if ($record->reservation->status !== 'Confirmed') {
//                             Notification::make()->title('Reservation is not confirmed')->danger()->send();
//                             return;
//                         }

//                         // Check if the room is clean
//                         if ($record->reservation->room->is_clean != 1) {
//                             Notification::make()
//                                 ->title('Room is not cleaned yet!')
//                                 ->danger()
//                                 ->send();

//                             return;
//                         }

//                         // Update check-in time and reservation status
//                         $record->update(attributes: [
//                             'check_in_time' => Carbon::now(),
//                             'status' => 'Checked In',
//                         ]);

//                         // Update the reservation status to 'Checked In'
//                         $record->reservation->update(['status' => 'Checked In']);

//                         // Mark room as unavailable
//                         $record->reservation->room->update(['status' => 0]);

//                         Notification::make()
//                             ->title('Check-In Successful!')
//                             ->success()
//                             ->send();
//                     })
//                     ->requiresConfirmation()
//                     ->visible(fn($record) => $record->status === 'Pending'),

//                 Tables\Actions\Action::make('Check Out')
//                     ->icon('heroicon-o-arrow-left-end-on-rectangle')
//                     ->action(function (CheckInCheckOut $record) {
//                         if ($record->status === 'Checked Out') {
//                             Notification::make()->title('Already Checked Out!')->warning()->send();
//                             return;
//                         }

//                         // Ensure the guest has been checked in before allowing check-out
//                         if ($record->status !== 'Checked In') {
//                             Notification::make()->title('Guest must be checked in before checking out!')->danger()->send();
//                             return;
//                         }

//                         // Update check-out time and reservation status
//                         $record->update([
//                             'check_out_time' => Carbon::now(),
//                             'status' => 'Checked Out',
//                         ]);

//                         // Update the reservation status to 'Checked Out'
//                         $record->reservation->update(['status' => 'Checked Out']);

//                         // Mark room as dirty after check-out
//                         $record->reservation->room->update(['is_clean' => 0, 'status' => 0]);

//                         Notification::make()
//                             ->title('Check-Out Successful! Room marked as dirty.')
//                             ->success()
//                             ->send();
//                     })
//                     ->requiresConfirmation()
//                     ->visible(fn($record) => $record->status === 'Checked In'),


//             ])
//             ->bulkActions([
//                 Tables\Actions\DeleteBulkAction::make(),
//             ]);
//     }

//     // PAGE ROUTES
//     public static function getPages(): array
//     {
//         return [
//             'index' => Pages\ListCheckInCheckOuts::route('/'),
//             'create' => Pages\CreateCheckInCheckOut::route('/create'),
//             'edit' => Pages\EditCheckInCheckOut::route('/{record}/edit'),
//         ];
//     }
// }


