<?php

namespace App\Filament\Management\Resources;

use App\Filament\Management\Resources\CouponManagementResource\Pages;
use App\Filament\Management\Resources\CouponManagementResource\RelationManagers;
use App\Models\CouponManagement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\EditAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CouponManagementResource extends Resource
{
    protected static ?string $model = CouponManagement::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Coupons';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?string $pluralModelLabel = 'Coupons';

    // public static function getLabel(): string
    // {
    //     return 'Marketing';
    // }
    public static function form(Form $form): Form
    {
        return $form
        ->schema(components: [

        Forms\Components\Card::make()

            ->schema(components: [
                // Coupon Details Section
                Forms\Components\Card::make()
                    ->schema([
                        TextInput::make('code')
                            ->label('Coupon Code')
                            ->required()
                            ->unique(ignorable: fn ($record) => $record),
                        
                        Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Brief description of the coupon')
                            ->required(),
                    ])
                    ->columnSpan(2), // Makes the card span two columns
                
                // Discount Details Section
                Forms\Components\Card::make()
                    ->schema([
                        Select::make('discount_type')
                            ->label('Discount Type')
                            ->options([
                                'percentage' => 'Percentage',
                                'fixed' => 'Fixed Amount',
                            ])
                            ->required()
                            ->reactive(), // To trigger live changes when selected
                        
                        TextInput::make('discount_percentage')
                            ->label('Discount Percentage (%)')
                            ->numeric()
                            ->placeholder('Enter percentage')
                            ->visible(fn ($get) => $get('discount_type') === 'percentage') // Show only if type is percentage
                            ->required(fn ($get) => $get('discount_type') === 'percentage'),
    
                        TextInput::make('discount_amount')
                            ->label('Discount Amount (₦)')
                            ->numeric()
                            ->placeholder('Enter discount amount')
                            ->visible(fn ($get) => $get('discount_type') === 'fixed') // Show only if type is fixed
                            ->required(fn ($get) => $get('discount_type') === 'fixed'),
                    ])
                    ->columns(2), // Make this section two columns wide
    
                // Validity Period Section
                Forms\Components\Card::make()
                    ->schema([
                        DatePicker::make('valid_from')
                            ->label('Valid From')
                            ->required(),
    
                        DatePicker::make('valid_until')
                            ->label('Valid Until')
                            ->required(),
                    ])
                    ->columns(2),
    
                // Usage Limit Section
                Forms\Components\Card::make()
                    ->schema([
                        TextInput::make('usage_limit')
                            ->label('Usage Limit')
                            ->numeric()
                            ->placeholder('Max uses allowed')
                            ->required(),
    
                        TextInput::make('times_used')
                            ->label('Times Used')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
    
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(2),
            ])
            ->columns(2)
                            ]);

    }
    

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('code')
                ->label('Coupon Code')
                ->searchable()
                ->sortable(),

            TextColumn::make('description')
                ->label('Description')
                ->limit(50),

            BadgeColumn::make('discount_type')
                ->label('Type')
                ->colors([
                    'primary' => 'percentage',
                    'success' => 'fixed',
                ]),

            // Conditional column display based on discount type
            TextColumn::make('discount_value')
                ->label('Discount')
                ->getStateUsing(function ($record) {
                    return $record->discount_type === 'percentage' 
                        ? $record->discount_percentage . '%' 
                        : '₦' . number_format($record->discount_amount, 2);
                })
                ->sortable(),

            TextColumn::make('valid_from')
                ->label('Valid From')
                ->date()
                ->sortable(),

            TextColumn::make('valid_until')
                ->label('Valid Until')
                ->date()
                ->sortable(),

            TextColumn::make('usage_limit')
                ->label('Usage Limit')
                ->sortable(),

            TextColumn::make('times_used')
                ->label('Times Used')
                ->sortable(),

            BadgeColumn::make('status')
                ->label('Status')
                ->colors([
                    'success' => 'active',
                    'danger' => 'inactive',
                ]),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),

                // TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
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
            'index' => Pages\ListCouponManagement::route('/'),
            'create' => Pages\CreateCouponManagement::route('/create'),
            'edit' => Pages\EditCouponManagement::route('/{record}/edit'),
        ];
    }
}
