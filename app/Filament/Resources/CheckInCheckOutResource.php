<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CheckInCheckOutResource\Pages;
use App\Filament\Resources\CheckInCheckOutResource\RelationManagers;
use App\Models\CheckInCheckOut;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;


class CheckInCheckOutResource extends Resource
{
    protected static ?string $model = CheckInCheckOut::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-left-end-on-rectangle';
    protected static ?string $navigationGroup = 'Operations Management';
    protected static ?string $navigationLabel = 'Check-In/Check-Out';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               
                    Select::make('reservation_id')
                    ->label('Reservation')
                    ->preload()
                    ->searchable()
                    ->options(Reservation::query()->pluck('guest_id', 'id')->toArray())
                    ->required(),               
                DateTimePicker::make('check_in_time')
                    ->label('Check-In Time')
                    ->required(),
                DateTimePicker::make('check_out_time')
                    ->label('Check-Out Time')
                    ->nullable(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'Pending' => 'Pending',
                        'Checked In' => 'Checked In',
                        'Checked Out' => 'Checked Out',
                    ])
                    ->default('Pending')
                    ->required(),
             
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reservation.guest.name')
                    ->label('Guest Name')
                    ->sortable()
                    ->searchable(),
                // TextColumn::make('reservation.room.room_number')
                //     ->label('Room Number')
                //     ->sortable(),
                TextColumn::make('check_in_time')
                    ->label('Check-In Time')
                    ->sortable(),
                TextColumn::make('check_out_time')
                    ->label('Check-Out Time')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->sortable(),
              
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Checked In' => 'Checked In',
                        'Checked Out' => 'Checked Out',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                
                Tables\Actions\Action::make('Check In')
                    ->icon('heroicon-o-login')
                    ->action(function (CheckInCheckOut $record, array $data) {
                        if ($record->status === 'Checked In') {
                            Notification::make()->title('Already Checked In!')->warning()->send();
                            return;
                        }
                        
                        $record->update([
                            'check_in_time' => Carbon::now(),
                            'status' => 'Checked In',
                        ]);

                        Notification::make()
                            ->title('Check-In Successful!')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'Pending'),
                
                    Tables\Actions\Action::make('Check Out')
                    ->icon('heroicon-o-logout')
                    ->action(function (CheckInCheckOut $record, array $data) {
                        if ($record->status === 'Checked Out') {
                            Notification::make()->title('Already Checked Out!')->warning()->send();
                            return;
                        }

                        $record->update([
                            'check_out_time' => Carbon::now(),
                            'status' => 'Checked Out',
                        ]);

                        Notification::make()
                            ->title('Check-Out Successful!')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'Checked In'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCheckInCheckOuts::route('/'),
            'create' => Pages\CreateCheckInCheckOut::route('/create'),
            'edit' => Pages\EditCheckInCheckOut::route('/{record}/edit'),
        ];
    }
}

// class CheckInCheckOutResource extends Resource
// {
//     protected static ?string $model = CheckInCheckOut::class;

//     protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

//     public static function form(Form $form): Form
//     {
//         return $form
//             ->schema([
//                 //
//             ]);
//     }

//     public static function table(Table $table): Table
//     {
//         return $table
//             ->columns([
//                 //
//             ])
//             ->filters([
//                 //
//             ])
//             ->actions([
//                 Tables\Actions\EditAction::make(),
//             ])
//             ->bulkActions([
//                 Tables\Actions\BulkActionGroup::make([
//                     Tables\Actions\DeleteBulkAction::make(),
//                 ]),
//             ]);
//     }

//     public static function getRelations(): array
//     {
//         return [
//             //
//         ];
//     }

//     public static function getPages(): array
//     {
//         return [
//             'index' => Pages\ListCheckInCheckOuts::route('/'),
//             'create' => Pages\CreateCheckInCheckOut::route('/create'),
//             'edit' => Pages\EditCheckInCheckOut::route('/{record}/edit'),
//         ];
//     }
// }
