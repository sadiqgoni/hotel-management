<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Filament\Resources\RoomResource\RelationManagers;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\StaffManagement;
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
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
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
                    ->money('NGN'),
                TextColumn::make('max_occupancy')
                    ->label('Max Occupancy')
                    ->sortable()
                    ->searchable(),

                IconColumn::make('status')
                    ->label('Availability')
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        '1' => 'Available',
                        '0' => 'Unavailable',
                    ])
                    ->searchable()
            ])
            ->actions([
                Tables\Actions\Action::make('toggleCleaningStatus')
                ->label(fn(Room $record) => $record->is_clean ? 'Mark as Dirty' : 'Mark as Clean')
                ->tooltip(fn(Room $record) => $record->is_clean ? 'Set room as dirty' : 'Set room as clean')
                ->icon(fn(Room $record) => $record->is_clean ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn(Room $record) => $record->is_clean ? 'danger' : 'success')
                ->action(function (Room $record) {
                    $record->is_clean = !$record->is_clean;
                    $record->save();
                }),
                Tables\Actions\Action::make('assignHousekeeper')
                    ->label('Assign Housekeeper')
                    ->icon('heroicon-o-user-plus')
                    ->visible(fn(Room $record) => !$record->housekeeper_id && !$record->is_clean)
                    ->modalHeading('Assign Housekeeper')
                    ->form([
                        Select::make('housekeeper_id')
                            ->label('Housekeeper')
                            ->options(StaffManagement::where('role', 'housekeeper')->pluck('full_name', 'id')) 
                            ->required(),
                        TextInput::make('note')
                            ->label('Special Instructions')
                            ->placeholder('Add any notes or special instructions for the housekeeper')
                            ->nullable(),
                    ])
                    ->action(function (Room $record, array $data) {
                        // Assign housekeeper and save any notes or instructions
                        $record->housekeeper_id = $data['housekeeper_id'];
                        $record->save();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
