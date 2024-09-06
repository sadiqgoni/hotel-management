<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuestResource\Pages;
use App\Filament\Resources\GuestResource\RelationManagers;
use App\Models\Guest;
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
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GuestResource extends Resource
{
    protected static ?string $model = Guest::class;


    protected static ?string $navigationGroup = 'Bookings & Guests Management';
    protected static ?string $navigationLabel = 'Guest Records';
    protected static ?string $modelLabel = 'Guest Records';
    protected static ?string $navigationIcon = 'heroicon-o-identification';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Section::make('Personal Information')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->label('Full Name')
                                    ->placeholder('Enter first name')
                                    ->maxLength(255),
                                TextInput::make('phone_number')
                                    ->nullable()
                                    ->label('Phone Number')
                                    ->placeholder('Enter phone number')
                                    ->maxLength(255),
                                TextInput::make('nin_number')
                                    ->label('NIN Number')
                                    ->placeholder('Enter NIN')
                                    ->maxLength(255),
                                TextInput::make('bonus_code')
                                    ->label('Bonus Code')
                                    ->placeholder('Enter Bonus Code')
                                    ->maxLength(255),
                                Textarea::make('preferences')
                                    ->label('Preferences')
                                    ->placeholder('Enter Preferences'),
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
                TextColumn::make('name')
                    ->label('Full Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('phone_number')
                    ->label('Phone Number')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nin_number')
                    ->label('NIN Number')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('bonus_code')
                    ->label('Bonus Code')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('preferences')
                    ->label('Preferences')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListGuests::route('/'),
            'create' => Pages\CreateGuest::route('/create'),
            'edit' => Pages\EditGuest::route('/{record}/edit'),
        ];
    }
}
