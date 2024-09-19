<?php
namespace App\Filament\Frontdesk\Resources;

use App\Filament\Frontdesk\Resources\ReservationWaitlistResource\Pages;
use App\Filament\Frontdesk\Resources\Frontdesk\ReservationWaitlistResource\RelationManagers;
use App\Models\Guest;
use App\Models\ReservationWaitlist;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Room;
use App\Models\RoomType;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;

class ReservationWaitlistResource extends Resource
{
    protected static ?string $model = ReservationWaitlist::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Reservation Management';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([

                        Select::make('guest_id')
                            ->label('Select Guest')
                            ->preload()
                            ->required()
                            ->searchable()
                            ->options(Guest::pluck('name', 'id')->toArray())
                            ->placeholder('Select an existing guest or create a new one')
                            ->reactive()  // Allows for dynamic updates when a guest is selected

                            ->createOptionForm([
                                TextInput::make('name')->label('Full Name')->required()->maxLength(255),
                                TextInput::make('phone_number')->label('Phone Number')->unique(Guest::class, 'phone_number')->maxLength(255),
                                TextInput::make('nin_number')->label('NIN Number')->unique(Guest::class, 'nin_number')->maxLength(255),
                                Textarea::make('preferences')->label('Preferences')->placeholder('E.g., Halal food, quiet room'),
                            ])
                            ->createOptionAction(function (Action $action) {
                                return $action->modalHeading('Create New Guest')->modalButton('Add Guest')->modalWidth('lg');
                            })
                            ->createOptionUsing(function ($data) {
                                return Guest::create($data)->id;
                            }),



                        Select::make('waitlist_option')
                            ->label('Waitlist Option')
                            ->options([
                                'room_type' => 'Room Type',
                                'specific_room' => 'Specific Room',
                            ])
                            ->searchable()
                            ->reactive()
                            ->required(),

                        // Room Type Selection
                        Select::make('room_type_id')
                            ->label('Room Type')
                            ->searchable()
                            ->options(RoomType::all()->pluck('name', 'id'))
                            ->placeholder('Select Room Type')
                            ->hidden(fn(callable $get) => $get('waitlist_option') !== 'room_type') // Hidden when specific room is selected
                            ->reactive()
                            ->afterStateUpdated(function (callable $set) {
                                $set('room_id', null); // Reset room selection when room type is selected
                            }),

                        // Room Selection
                        Select::make('room_id')
                            ->label('Room')
                            ->searchable()
                            ->options(Room::where('status', true)->pluck('room_number', 'id'))
                            ->placeholder('Select Room')
                            ->afterStateUpdated(function (callable $set) {
                                $set('room_type_id', null); // Reset room type selection when a specific room is selected
                            })
                            ->hidden(fn(callable $get) => $get('waitlist_option') !== 'specific_room') // Hidden when room type is selected
                            ->reactive(),



                        DatePicker::make('desired_check_in_date')
                            ->label('Desired Check-In Date')
                            ->required(),

                        DatePicker::make('desired_check_out_date')
                            ->label('Desired Check-Out Date')
                            ->required(),

                        Toggle::make('is_notified')
                            ->label('Guest Notified')
                            ->inline(false)
                            ->default(false),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                ->label('ID'),
                TextColumn::make('guest.name')
                ->label('Guest Name'),

                // Display Room Type or Room Number depending on which is selected
                TextColumn::make('waitlist_option')
                    ->label('Waitlist Option')
                    ->formatStateUsing(function ($record) {
                        if ($record->room_type_id) {
                            return "Room Type: " . $record->roomType->name;
                        } elseif ($record->room_id) {
                            return "Room Number: " . $record->room->room_number;
                        } else {
                            return "N/A";
                        }
                    }),

                TextColumn::make('desired_check_in_date')->label('Check-In Date')->date(),
                TextColumn::make('desired_check_out_date')->label('Check-Out Date')->date(),

                // Shows whether the guest has been notified
                IconColumn::make('is_notified')
                    ->label('Notified')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // Custom email action to notify guest
                Tables\Actions\Action::make('sendEmail')
                    ->label('Notify via Email')
                    ->icon('heroicon-o-envelope')
                    ->action(function ($record, callable $set) {
                        // Logic for sending email
                        // Example: Use a modal for frontdesk to enter a custom message
                        // Pre-fill message with reservation info
                    })
                    ->modalHeading('Send Email Notification')
                    ->form([
                        Forms\Components\Textarea::make('custom_message')
                            ->label('Custom Message')
                            ->placeholder('Type your message here...')
                            ->rows(4),
                    ])
                    ->action(function ($record, $data) {
                        $guest = $record->guest;
                        $customMessage = $data['custom_message'];
                        // Implement the logic to send email using system info and custom content
                        // For instance, combine reservation details with custom message
                        Notification::make()
                            ->title('Email Sent')
                            ->body("Reservation Waitlist Notification sent to {$guest->name}")
                            ->success()
                            ->send();
                    })

                    ->color('success'),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListReservationWaitlists::route('/'),
            'create' => Pages\CreateReservationWaitlist::route('/create'),
            'edit' => Pages\EditReservationWaitlist::route('/{record}/edit'),
        ];
    }
}

