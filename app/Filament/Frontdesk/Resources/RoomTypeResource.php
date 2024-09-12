<?php

namespace App\Filament\Frontdesk\Resources;

use App\Filament\Frontdesk\Resources\RoomTypeResource\Pages;
use App\Filament\Frontdesk\Resources\RoomTypeResource\RelationManagers;
use App\Models\RoomType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoomTypeResource extends Resource
{
    protected static ?string $model = RoomType::class;

    protected static ?string $navigationGroup = 'Rooms Management';
    protected static ?string $navigationLabel = 'Room Categories';
    protected static ?string $modelLabel = 'Room Categories';
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Forms\Components\Section::make('Room Type Details')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Room Type Name')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('Enter Room Type Name'),
                                TextInput::make('base_price')
                                    ->label('Base Price')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('Enter Base Price'),
                                    TextInput::make('max_occupancy')
                                    ->label('Max Occupancy')
                                    ->required(),
                                    Textarea::make('description')
                                    ->label('Description')
                                    ->placeholder('Enter Description'),
                               
                            ])
                            ->columnSpan(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Room Type Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('base_price')
                    ->label('Base Price')
                    ->sortable()
                    ->searchable(),
                    TextColumn::make('max_occupancy')
                    ->label('Max Occupancy')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListRoomTypes::route('/'),
            'create' => Pages\CreateRoomType::route('/create'),
            'edit' => Pages\EditRoomType::route('/{record}/edit'),
        ];
    }
}

