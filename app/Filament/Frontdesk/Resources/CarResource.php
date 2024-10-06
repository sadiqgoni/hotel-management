<?php

namespace App\Filament\Frontdesk\Resources;

use App\Filament\Frontdesk\Resources\CarResource\Pages;
use App\Models\Car;
use App\Models\Guest;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms;
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


class CarResource extends Resource
{
    protected static ?string $model = Car::class;



    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Transport Management';


    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Section::make('')
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
                                TextInput::make('rate_per_hour')
                                    ->label('Rate Per Hour')
                                    ->numeric()
                                    ->required(),
                            ])
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

                TextColumn::make('car_name')->label('Car Name'),
                TextColumn::make('car_type')->label('Car Type'),
                TextColumn::make('number_plate')->label('Number Plate'),
                TextColumn::make('availability_status')->label('Status')->sortable(),
                TextColumn::make('rate_per_hour')->label('Rate Per Hour')->money('NGN', true)

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
            'index' => Pages\ListCars::route('/'),
            'create' => Pages\CreateCar::route('/create'),
            'edit' => Pages\EditCar::route('/{record}/edit'),
        ];
    }
}

