<?php

namespace App\Filament\Frontdesk\Resources;

use App\Filament\Frontdesk\Resources\CarRentalResource\Pages;
use App\Filament\Resources\CarRentalResource\RelationManagers;
use App\Models\Car;
use App\Models\CarRental;
use App\Models\Guest;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Card;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class CarRentalResource extends Resource
{
    protected static ?string $model = CarRental::class;


    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Transport Management';
    protected static ?int $navigationSort = 2;



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Section::make('')
                            ->schema([
                                Select::make('guest_id')
                                ->label('Guest')
                                ->searchable()
                                ->options(Guest::all()->pluck('name', 'id'))
                                ->required(),
                            Select::make('car_id')
                                ->label('Car')
                                ->options(Car::all()->pluck('car_name', 'id'))
                                ->required(),
                            DatePicker::make('rented_at')
                                ->label('Rental At')
                                ->required(),
                            DatePicker::make('returned_at')
                                ->label('Returned At')
                                ->required(),
                            ]),
                            TextInput::make('total_cost')
                            ->label('Total Cost')
                            ->columns(2),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('guest.name')->label('Guest'),
                TextColumn::make('car.car_name')->label('Car'),
                TextColumn::make('rented_at')
                ->date()
                ->label('Rented At'),
                TextColumn::make('returned_at')
                ->date()
                ->label('Returned At'),
                TextColumn::make('total_cost')->label(
                    'Total Amount'
                )->money('NGN', true),
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
            'index' => Pages\ListCarRentals::route('/'),
            'create' => Pages\CreateCarRental::route('/create'),
            'edit' => Pages\EditCarRental::route('/{record}/edit'),
        ];
    }
}

