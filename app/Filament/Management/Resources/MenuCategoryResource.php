<?php

namespace App\Filament\Management\Resources;

use App\Filament\Management\Resources\MenuCategoryResource\Pages;
use App\Filament\Management\Resources\MenuCategoryResource\RelationManagers;
use App\Models\MenuCategory;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;


class MenuCategoryResource extends Resource
{
    protected static ?string $model = MenuCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationLabel = 'Menu Categories';
    protected static ?string $navigationGroup = 'Menu Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->schema([
                        TextInput::make('name')
                            ->label('Category Name')
                            ->placeholder('Enter category name')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Category Description')
                            ->placeholder('Enter a brief description')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Provide a short description about this category.'),
                        // Icon selection dropdown
                        Select::make('icon')
                            ->label('Category Icon')
                            ->options([
                                '🍲' => '🍲',
                                '🍜' => '🍜',
                                '🥣' => '🥣',
                                '🍽️' => '🍽️',
                                '🥗' => '🥗',
                                '☕' => '☕',
                                '🍔' => '🍔',
                                '🍗' => '🍗',
                                '🍟' => '🍟',
                                '🍕' => '🍕',
                                '🍳' => '🍳',
                                '🥤' => '🥤',
                                '🍰' => '🍰',
                            ])
                            ->required()
                            ->helperText('Choose an appropriate icon for this category.'),
                    ])
                    ->collapsible()
                 
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Category Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50),
                TextColumn::make('icon')
                    ->label('Icon')
                    ->formatStateUsing(fn($state) => $state), // To display the icon in the table

            ])
            ->filters([
                // Add any filters if necessary
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenuCategories::route('/'),
            'create' => Pages\CreateMenuCategory::route('/create'),
            'edit' => Pages\EditMenuCategory::route('/{record}/edit'),
        ];
    }


}
