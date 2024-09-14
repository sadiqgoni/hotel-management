<?php

namespace App\Filament\Frontdesk\Resources;

use App\Filament\Frontdesk\Resources\RoomResource\Pages;
use App\Filament\Frontdesk\Resources\RoomResource\RelationManagers;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\StaffManagement;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationGroup = 'Rooms Management';
    protected static ?string $navigationLabel = 'Manage Rooms';
    protected static ?string $modelLabel = 'Manage Rooms';
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Section::make('Room Details')
                            ->schema([
                                Select::make('room_type_id')
                                    ->label('Room Type')
                                    ->options(RoomType::all()->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->placeholder('Select Room Type')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        // Fetch the selected Room Type
                                        $roomType = RoomType::find($state);

                                        if ($roomType) {
                                            // Set the price per night and max occupancy based on the selected Room Type
                                            $set('price_per_night', $roomType->base_price);
                                            $set('max_occupancy', $roomType->max_occupancy);
                                            $set('description', $roomType->description);
                                            // Generate the next room number based on the room type name
                                            $roomPrefix = strtoupper(substr($roomType->name, 0, 3)); // Get first 3 letters
                                            $latestRoomNumber = Room::where('room_type_id', $state)
                                                ->orderBy('room_number', 'desc')
                                                ->first()?->room_number;
                                            // Increment the last room number
                                            if ($latestRoomNumber) {
                                                $numberPart = (int) substr($latestRoomNumber, 3); // Extract the number part
                                                $newRoomNumber = $roomPrefix . str_pad($numberPart + 1, 3, '0', STR_PAD_LEFT);
                                            } else {
                                                $newRoomNumber = $roomPrefix . '001'; // First room number
                                            }

                                            $set('room_number', $newRoomNumber);
                                        }
                                    }),

                                TextInput::make('room_number')
                                    ->label('Room Number')
                                    ->readOnly()
                                    ->placeholder('Auto-generated Room Number'),

                                TextInput::make('price_per_night')
                                    ->label('Price per Night')
                                    ->placeholder('Auto-filled based on Room Type')
                                    ->readOnly(),

                                TextInput::make('max_occupancy')
                                    ->label('Max Occupancy')
                                    ->placeholder('Auto-filled based on Room Type')
                                    ->readOnly(),
                                TextInput::make('description')
                                    ->label('Description')
                                    ->placeholder('Auto-filled based on Room Type')
                                    ->readOnly(),

                                Forms\Components\Toggle::make('status')
                                    ->label('Availability')
                                    ->default(1)
                                    ->live(onBlur: true)
                                    ->helperText('Toggle this to mark the room as available or unavailable.')
                                    ->disabled(fn($get) => Reservation::where('room_id', $get('id'))->whereIn('status', ['Confirmed', 'Checked In'])->exists()),

                            ])
                            ->columns(2),
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        $user = auth()->user(); // Get the current logged-in user

        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->visible(fn() => $user->role !== 'Housekeeper') // Hide for Housekeeper
                    ->searchable(),
                TextColumn::make('room_number')
                    ->label('Room Number')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('roomType.name')
                    ->label('Room Type')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('price_per_night')
                    ->label('Price per Night')
                    ->sortable()
                    ->visible(fn() => $user->role !== 'Housekeeper') // Hide for Housekeeper
                    ->money('NGN'),
                TextColumn::make('max_occupancy')
                    ->label('Max Occupancy')
                    ->visible(condition: fn() => $user->role !== 'Housekeeper') // Hide for Housekeeper
                    ->sortable(),
                BadgeColumn::make('is_clean')
                    ->label('Cleaning Status')
                    ->color(fn(string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                        '2' => 'warning',
                    })
                    ->default('0')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            0 => 'Dirty',
                            1 => 'Clean',
                            2 => 'In Progress',
                            default => 'Unknown',
                        };
                    })
                    ->icons([
                        'heroicon-o-x-circle' => 0,    // Icon for "Dirty"
                        'heroicon-o-sparkles' => 1,    // Icon for "Clean"
                        'heroicon-o-exclamation-circle' => 2,  // Icon for "In Progress"
                    ]),
                IconColumn::make('status')
                    ->label('Availability')
                    ->visible(fn() => $user->role !== 'Housekeeper') // Hide for Housekeeper
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->sortable(),
                    TextColumn::make('note')
                    
                    ->label('Special Instructions')
                    ->visible(fn() => $user->role !== 'FrontDesk') 
                    ->default('None')
                    ->limit(50),
                TextColumn::make('description')
                    ->label('Description')
                    ->visible(fn() => $user->role !== 'Housekeeper') 
                    ->limit(50),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        '1' => 'Available',
                        '0' => 'Unavailable',
                    ])
                    ->searchable(),
                SelectFilter::make('assigned_rooms')
                    ->label('Assigned Rooms')
                    ->query(function (Builder $query) use ($user) {
                        if ($user->role === 'Housekeeper') {
                            $query->where('housekeeper_id', $user->id);
                        }
                    })
                    ->searchable()
                    ->hidden(fn() => $user->role !== 'Housekeeper'), // Hide this filter for non-housekeepers
            ])
            ->actions([
                // Frontdesk: Mark room as dirty and assign housekeeper
                Tables\Actions\Action::make('markAsDirtyAndAssignHousekeeper')
                    ->label('Mark as Dirty & Assign Housekeeper')
                    ->icon('heroicon-o-user-plus')
                    ->visible(fn(Room $record) => $user->role === 'FrontDesk')
                    ->action(function (Room $record, array $data) {
                        $record->is_clean = 0; // Mark as dirty
                        $record->status = 0;
                        $record->housekeeper_id = $data['housekeeper_id']; // Assign housekeeper
                        $record->save();
                    })
                    ->form([
                        Select::make('housekeeper_id')
                            ->label('Assign Housekeeper')
                            ->options(User::where('role', 'Housekeeper')->pluck('name', 'id'))
                            ->required(),
                            TextInput::make('note')
                            ->label('Special Instructions')
                            // ->action(function (Room $record, array $data) {
                            //     $record->note = $data['note']; 
                            //     $record->save();
                            // })
                            ->placeholder('Add any notes or special instructions for the housekeeper')
                            ->nullable(),
                    ]),

                // Housekeeper: Mark room as "In Progress" when cleaning starts
                Tables\Actions\Action::make('startCleaning')
                    ->label('Start Cleaning')
                    ->icon('heroicon-o-play')
                    ->visible(fn(Room $record) => $user->role === 'Housekeeper' && $record->is_clean == 0 && $record->housekeeper_id == $user->id) // Only for assigned dirty rooms
                    ->action(function (Room $record) {
                        $record->is_clean = 2; // Mark as "In Progress"
                        $record->save();
                    }),

                // Housekeeper: Mark room as clean after cleaning is done
                Tables\Actions\Action::make('finishCleaning')
                    ->label('Finish Cleaning')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn(Room $record) => $user->role === 'Housekeeper' && $record->is_clean == 2 && $record->housekeeper_id == $user->id) // Only for rooms "in progress"
                    ->action(function (Room $record) {
                        $record->is_clean = 1; // Mark as clean
                        $record->save();
                    }),

                // Frontdesk: Acknowledge the cleaning completion
                Tables\Actions\Action::make('acknowledgeCleaning')
                    ->label('Acknowledge Cleaning')
                    ->icon('heroicon-o-hand-thumb-up')
                    ->visible(fn(Room $record) => $user->role === 'FrontDesk' && $record->is_clean == 1) // Only for clean rooms
                    ->action(function (Room $record) {
                        $record->status = 1; // Mark room as available again
                        $record->save();
                    }),
                // Edit action (Visible only to Frontdesk)
                Tables\Actions\EditAction::make()
                    ->visible(fn() => $user->role === 'FrontDesk'),

                // View action (Visible to both)
                Tables\Actions\ViewAction::make()
                ->visible(condition: fn() => $user->role === 'FrontDesk'),


                // Delete action (Visible only to Frontdesk)
                Tables\Actions\DeleteAction::make()
                    ->visible(condition: fn() => $user->role === 'FrontDesk'),
            ])
            ->bulkActions([]);
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
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
