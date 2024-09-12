<?php

namespace App\Filament\Management\Resources;

use App\Filament\Management\Resources\StaffManagementResource\Pages;
use App\Filament\Management\Resources\StaffManagementResource\RelationManagers;
use App\Models\StaffManagement;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


class StaffManagementResource extends Resource
{
    protected static ?string $model = StaffManagement::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        // Personal Information Section
                        Forms\Components\Section::make('Personal Information')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('full_name')
                                            ->required()
                                            ->label('Full Name')
                                            ->placeholder('Enter full name')
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('email')
                                            ->required()
                                            ->label('Email Address')
                                            ->email()
                                            ->unique(StaffManagement::class, 'email', ignoreRecord: true)
                                            ->placeholder('Enter email address'),

                                        Forms\Components\TextInput::make('phone_number')
                                            ->label('Phone Number')
                                            ->tel()
                                            ->maxLength(20)
                                            ->placeholder('Enter contact number'),


                                        Forms\Components\FileUpload::make('profile_picture')
                                            ->label('Profile Picture')
                                            ->disk('public')
                                            ->image()
                                            ->directory('profile-pictures')  // Directory to store uploaded images
                                            ->placeholder('Upload a profile picture'),
                                          
                                    ]),
                            ]),

                        // Employment Details Section
                        Forms\Components\Section::make('Employment Details')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('role')
                                            ->required()
                                            ->label('Role')
                                            ->options([
                                                'Manager' => 'Manager',
                                                'Receptionist' => 'Receptionist',
                                                'Housekeeper' => 'Housekeeper',
                                                'Security' => 'Security',
                                                'Maintenance' => 'Maintenance',
                                            ])
                                            ->searchable()
                                            ->placeholder('Select role'),

                                        Forms\Components\DatePicker::make('employment_date')
                                            ->label('Employment Date')
                                            ->required()
                                            ->placeholder('Select employment date'),

                                        Forms\Components\DatePicker::make('termination_date')
                                            ->label('Termination Date')
                                            ->placeholder('Select termination date'),

                                        Forms\Components\TextInput::make('shift')
                                            ->label('Shift')
                                            ->placeholder('Morning, Evening, Night'),
                                    ]),
                            ]),

                        // Additional Information Section
                        Forms\Components\Section::make('Additional Information')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Textarea::make('address')
                                            ->label('Address')
                                            ->placeholder('Enter address'),

                                        Forms\Components\Select::make('status')
                                            ->label('Employment Status')
                                            ->options([
                                                'active' => 'Active',
                                                'suspended' => 'Suspended',
                                                'terminated' => 'Terminated',
                                            ])
                                            ->searchable()

                                            ->default('active')
                                            ->required(),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                ->label('ID')
                ->searchable()
                ->sortable(),
                ImageColumn::make('profile_picture')
                ->label('Profile Picture')
                ->rounded() // Optionally make the image rounded
                ->width(50) // Set width of the image
                ->height(50), // Set height of the image



                TextColumn::make('full_name')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('role')
                    ->label('Role')
                    ->colors([
                        'primary' => 'Manager',
                        'success' => 'Receptionist',
                        'warning' => 'Housekeeper',
                        'info' => 'Security',
                        'danger' => 'Maintenance',
                    ]),

                TextColumn::make('phone_number')
                    ->label('Phone')
                    ->sortable(),

                TextColumn::make('shift')
                    ->label('Shift'),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'suspended',
                        'warning' => 'terminated',
                    ]),

                TextColumn::make('employment_date')
                    ->label('Employment Date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter by Status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'terminated' => 'Terminated',
                    ]),
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
            'index' => Pages\ListStaffManagement::route('/'),
            'create' => Pages\CreateStaffManagement::route('/create'),
            'edit' => Pages\EditStaffManagement::route('/{record}/edit'),
        ];
    }
}