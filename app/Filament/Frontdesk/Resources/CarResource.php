<?php

namespace App\Filament\Frontdesk\Resources;

use App\Filament\Frontdesk\Resources\CarResource\Pages;
use App\Filament\Frontdesk\Resources\CarResource\RelationManagers;
use App\Models\Car;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CarResource extends Resource
{
    protected static ?string $model = Car::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Transport Management';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                ->schema([
                    Select::make('guest_id')
                    ->label('Guest')
                    ->relationship('guest', 'name') // Assuming you have a guest model
                    ->required(),
                Select::make('car_id')
                    ->label('Car')
                    ->relationship('car', 'car_name')
                    ->required(),
                DatePicker::make('rental_start')
                    ->label('Rental Start')
                    ->required(),
                DatePicker::make('rental_end')
                    ->label('Rental End')
                    ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('guest.name')->label('Guest'),
                TextColumn::make('car.car_name')->label('Car'),
                TextColumn::make('rental_start')->label('Start Date'),
                TextColumn::make('rental_end')->label('End Date'),
                TextColumn::make('total_amount')->label('Total Amount')->money('NGN', true),
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
            'index' => Pages\ListCars::route('/'),
            'create' => Pages\CreateCar::route('/create'),
            'edit' => Pages\EditCar::route('/{record}/edit'),
        ];
    }
}
