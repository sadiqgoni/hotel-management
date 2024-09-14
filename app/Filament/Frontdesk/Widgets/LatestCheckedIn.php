<?php 
namespace App\Filament\Frontdesk\Widgets;

use App\Models\CheckInCheckOut;
use Carbon\Carbon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestCheckedIn extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';


    public function table(Table $table): Table
    {
        return $table
        ->query(CheckInCheckOut::query()->where('status', 'Checked In')->latest()) // Query records where status is "Checked In"

            ->columns([
               Split::make([
                    Stack::make([
                        TextColumn::make('reservation.guest.name')
                        ->description('Guest Name','above')
                            ->sortable()
                            ->weight('bold')
                            ->alignLeft(),

                            TextColumn::make('reservation.guest.phone_number')
                            ->searchable()
                            ->sortable()
                            ->color('gray')
                            ->alignLeft(),
                    ])->space(),

                   Stack::make([
                            TextColumn::make('reservation.room.room_number')
                            ->description('Room Number','above')
                            ->sortable()
                            ->alignLeft(),
                    ])->space(),
                    Stack::make([
                
                        TextColumn::make('check_in_time')
                        ->description('Check-In Time','above')
                        ->sortable()
                        ->alignLeft(),
             
                ])->space(),
                Stack::make([

                    TextColumn::make('check_out_time')
                    ->description('Check-Out Time','above')
                    ->sortable()
                    ->alignLeft(),
         
            ])->space(),
            Stack::make([
        
                BadgeColumn::make('status')
                ->description('Status','above')
                ->color(fn(string $state): string => match ($state) {
                    'Pending' => 'danger',
                    'Checked In' => 'success',
                    'Checked Out' => 'warning',
                })
                ->sortable(),
     
        ])->space(),
                ])->from('md'),
            ])
            ->filters([
                //
            ])
            ->actions([

                Tables\Actions\Action::make('Check In')
                    ->icon('heroicon-o-arrow-left-end-on-rectangle')
                    ->action(function (CheckInCheckOut $record) {
                        if ($record->status === 'Checked In') {
                            Notification::make()->title('Already Checked In!')->warning()->send();
                            return;
                        }

                        // Ensure the reservation is confirmed and has not already been checked in or out
                        if ($record->reservation->status !== 'Confirmed') {
                            Notification::make()->title('Reservation is not confirmed')->danger()->send();
                            return;
                        }

                        // Check if the room is clean
                        if ($record->reservation->room->is_clean != 1) {
                            Notification::make()
                                ->title('Room is not cleaned yet!')
                                ->danger()
                                ->send();

                            return;
                        }

                        // Update check-in time and reservation status
                        $record->update(attributes: [
                            'check_in_time' => Carbon::now(),
                            'status' => 'Checked In',
                        ]);

                        // Update the reservation status to 'Checked In'
                        $record->reservation->update(['status' => 'Checked In']);

                        // Mark room as unavailable
                        $record->reservation->room->update(['status' => 0]);

                        Notification::make()
                            ->title('Check-In Successful!')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->status === 'Pending'),

                Tables\Actions\Action::make('Check Out')
                    ->icon('heroicon-o-arrow-left-end-on-rectangle')
                    ->action(function (CheckInCheckOut $record) {
                        if ($record->status === 'Checked Out') {
                            Notification::make()->title('Already Checked Out!')->warning()->send();
                            return;
                        }

                        // Ensure the guest has been checked in before allowing check-out
                        if ($record->status !== 'Checked In') {
                            Notification::make()->title('Guest must be checked in before checking out!')->danger()->send();
                            return;
                        }

                        // Update check-out time and reservation status
                        $record->update([
                            'check_out_time' => Carbon::now(),
                            'status' => 'Checked Out',
                        ]);

                        // Update the reservation status to 'Checked Out'
                        $record->reservation->update(['status' => 'Checked Out']);

                        // Mark room as dirty after check-out
                        $record->reservation->room->update(['is_clean' => 0, 'status' => 0]);

                        Notification::make()
                            ->title('Check-Out Successful! Room marked as dirty.')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->status === 'Checked In'),

            ])
            ->groupedBulkActions([
                // Tables\Actions\DeleteBulkAction::make()
                //     ->action(function () {
                //         Notification::make()
                //             ->title('Now, now, don\'t be cheeky, leave some records for others to play with!')
                //             ->warning()
                //             ->send();
                //     }),
            ]);
    }
}
