<?php

namespace App\Filament\Frontdesk\Resources;

use App\Filament\Frontdesk\Resources\CarRentalResource\Pages;
use App\Filament\Resources\CarRentalResource\RelationManagers;
use App\Models\CarRental;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CarRentalResource extends Resource
{
    protected static ?string $model = CarRental::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Transport Management';


    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Card::make()
            ->schema([
                TextInput::make('car_name')
                ->label('Car Name')
                ->required(),
            TextInput::make('car_type')
                ->label('Car Type')
                ->required(),
            TextInput::make('number_plate')
                ->label('Number Plate')
                ->required(),
            Select::make('availability_status')
                ->label('Availability')
                ->options([
                    'available' => 'Available',
                    'rented' => 'Rented',
                    'maintenance' => 'Maintenance',
                ]),
            TextInput::make('rate_per_day')
                ->label('Rate Per Day')
                ->numeric()
                ->required(),
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('car_name')->label('Car Name'),
                TextColumn::make('car_type')->label('Car Type'),
                TextColumn::make('number_plate')->label('Number Plate'),
                TextColumn::make('availability_status')->label('Status')->sortable(),
                TextColumn::make('rate_per_day')->label('Rate Per Day')->money('NGN', true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListCarRentals::route('/'),
            'create' => Pages\CreateCarRental::route('/create'),
            'edit' => Pages\EditCarRental::route('/{record}/edit'),
        ];
    }
}
