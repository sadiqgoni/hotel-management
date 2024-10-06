<?php

namespace App\Filament\Frontdesk\Resources;

use App\Filament\Frontdesk\Resources\CheckInResource\Pages;
use App\Filament\Frontdesk\Resources\CheckInResource\RelationManagers;
use App\Models\CheckIn;
use App\Models\Reservation;
use App\Models\User;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Notifications\Collection;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CheckInResource extends Resource
{
    protected static ?string $model = CheckIn::class;

    protected static ?string $navigationGroup = 'Daily Operations';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Check In';
    protected static ?string $breadcrumb = 'Check In Guest';
    protected static ?string $modelLabel = 'Check In';


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID'),
                // Reservation ID column
                Tables\Columns\TextColumn::make('reservation_number')
                    ->label('Reservation ID')
                    ->sortable()
                    ->formatStateUsing(fn($state) => '#' . str_pad($state, 4, '0', STR_PAD_LEFT)),
                // Room Number column
                Tables\Columns\TextColumn::make('room_number')
                    ->label('Room Number')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('room_number')
                    ->label('Room Number')
                    ->sortable()
                    ->searchable(),

                // Guest Name column
                Tables\Columns\TextColumn::make('guest_name')
                    ->label('Guest Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('guest_phone')
                    ->label('Phone')
                    ->sortable()
                    ->searchable(),

                // Check-in Time column
                Tables\Columns\TextColumn::make('check_in_time')
                    ->label('Check-In Time')
                    ->dateTime()
                    ->sortable(),

                // Check-out Time column
                Tables\Columns\TextColumn::make('check_out_time')
                    ->label('Check-Out Time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('Paid Amount')
                    ->money('NGN')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('due_amount')
                    ->label('Due Amount')
                    ->sortable()
                    ->searchable()->money('NGN'),


                Tables\Columns\BadgeColumn::make('booking_status')
                    ->label('Booking Status')
                    ->colors([
                        'success' => 'Checked In',
                        'warning' => 'Checked Out',
                        'danger' => 'Cancelled',
                    ])
                    ->sortable(),


                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Payment Status')
                    ->colors([
                        'success' => 'Full Payment',
                        'warning' => 'Partial Payment',
                    ])
                    ->sortable(),

            ])
            ->actions([
                // "Check Out" action that redirects to the CheckOut page
                Tables\Actions\Action::make('checkOut')
                    ->label('Check Out')
                    ->icon('heroicon-o-arrow-left-end-on-rectangle')
                    ->color('success')
                    ->action(fn($record) => redirect('/frontdesk/check-out-page',))
                    ->requiresConfirmation()  // Optional: adds a confirmation dialog
                    ->tooltip('Proceed to check out this guest'),

                // View action
                Tables\Actions\ViewAction::make(),
            ])

            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                // Reservation Information Section
                \Filament\Infolists\Components\Section::make('Reservation Information')
                    ->schema([
                        Split::make([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('reservation_number')
                                        ->label('Reservation ID')
                                        ->formatStateUsing(fn($state) => '#' . str_pad($state, 4, '0', STR_PAD_LEFT)),

                                    TextEntry::make('guest_name')
                                        ->label('Guest Name'),

                                    TextEntry::make('guest_phone')
                                        ->label('Guest Phone'),
                                ]),
                        ]),
                    ]),

                // Check-In Details Section
                \Filament\Infolists\Components\Section::make('Check-In Details')
                    ->schema([
                        Split::make([
                            Grid::make(3)
                                ->schema([
                                    TextEntry::make('room_number')
                                        ->label('Room Number'),

                                    TextEntry::make('check_in_time')
                                        ->label('Check-In Time')
                                        ->dateTime(),

                                    TextEntry::make('check_out_time')
                                        ->label('Check-Out Time')
                                        ->dateTime(),
                                ]),
                        ]),
                    ]),

                // Payment Information Section
                \Filament\Infolists\Components\Section::make('Payment Information')
                    ->schema([
                        Split::make([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('total_amount')
                                        ->label('Total Amount')
                                        ->money('NGN'),

                                    TextEntry::make('paid_amount')
                                        ->label('Paid Amount')
                                        ->money('NGN'),

                                    TextEntry::make('due_amount')
                                        ->label('Due Amount')
                                        ->money('NGN'),

                                    TextEntry::make('payment_status')
                                        ->label('Payment Status'),
                                ]),
                        ]),
                    ]),

                // Additional Information Section
                \Filament\Infolists\Components\Section::make('Additional Information')
                    ->schema([
                        Split::make([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('coupon_management')
                                        ->label('Coupon Applied'),

                                    TextEntry::make('coupon_discount')
                                        ->label('Coupon Discount')
                                        ->money('NGN'),

                                    TextEntry::make('special_requests')
                                        ->label('Special Requests'),
                                    TextEntry::make('frequent_guest_message')
                                        ->label('Frequent Guest Message'),

                                ]),
                        ]),
                    ]),

            ]);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCheckIns::route('/'),
            'create' => Pages\CreateCheckIn::route('/create'),
            'edit' => Pages\EditCheckIn::route('/{record}/edit'),
            'view' => Pages\ViewCheckIn::route('/{record}'),

        ];
    }
}
