<?php

namespace App\Filament\Management\Resources;

use App\Filament\Management\Resources\MenuItemResource\Pages;
use App\Filament\Management\Resources\MenuItemResource\RelationManagers;
use App\Models\MenuItem;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\MenuCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;


class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';
    protected static ?string $navigationLabel = 'Menu Items';
    protected static ?string $navigationGroup = 'Menu Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('menu_category_id')
                    ->label('Category')
                    ->options(MenuCategory::all()->pluck('name', 'id'))
                    ->required()
                    ->placeholder('Select a Category'),

                TextInput::make('name')
                    ->label('Item Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter item name'),

                Textarea::make('description')
                    ->label('Item Description')
                    ->rows(3)
                    ->placeholder('Enter a brief description')
                    ->maxLength(500),

                TextInput::make('price')
                    ->label('Price (₦)')
                    ->numeric()
                    ->required()
                    ->placeholder('Enter price'),

                FileUpload::make('image')
                    ->label('Item Image')
                    ->directory('menu-items')
                    ->image()
                    ->maxSize(1024)
                    ->placeholder('Upload image'),

           
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->square()
                    ->size(40),
                    // ->placeholder(fn() => asset('hotel2.png')),

                TextColumn::make('name')
                    ->label('Item Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('price')
                    ->label('Price (₦)')
                    ->sortable(),

            ])
            ->filters([
                SelectFilter::make('menu_category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),
            ])
            ->actions([
              EditAction::make(),
             DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::$model::where('is_available', true)->count();
    }
}
