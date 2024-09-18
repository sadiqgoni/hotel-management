<?php

namespace App\Filament\Housekeeper\Resources;

use App\Filament\Housekeeper\Resources\MaintenanceRequestResource\Pages;
use App\Filament\Housekeeper\Resources\MaintenanceRequestResource\RelationManagers;
use App\Models\MaintenanceRequest;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaintenanceRequestResource extends Resource
{
    protected static ?string $model = MaintenanceRequest::class;
    protected static ?string $navigationGroup = 'Rooms Management';

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->schema(
                        [
                            Forms\Components\TextInput::make('room_number')
                                ->label('Room Number'),
                            Forms\Components\Textarea::make('maintenance_details')
                                ->label('Maintenance Details'),
                        ]

                    )


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    TextColumn::make('created_by')
                        ->formatStateUsing(function ($state) {
                            $user = User::find($state);
                            return $user ? $user->name : 'Deleted User';
                        })
                        ->icon('heroicon-m-user')
                        ->hidden(condition: fn() => Auth::user()->role === 'Housekeeper'),

                    // ->visible(condition: fn() => Auth::user()->role === 'FrontDesk'),
                    Tables\Columns\TextColumn::make('room_number')
                        ->searchable()
                        ->weight('bold')
                        ->visible(condition: fn() => Auth::user()->role === 'Housekeeper'),

                    Tables\Columns\TextColumn::make('created_at')
                        ->date()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),

                ]),
                Panel::make([
                    Stack::make([
                        Tables\Columns\TextColumn::make('room_number')
                            ->searchable()
                            ->hidden(condition: fn() => Auth::user()->role === 'Housekeeper')
                            ->weight('bold'),
                        TextColumn::make('maintenance_details')
                            ->visible(condition: fn() => Auth::user()->role === 'Housekeeper')
                            ->searchable(),

                            Tables\Columns\TextColumn::make('status')
                            ->label('Current Status')
                            ->icon(fn ($state) => match ($state) {
                                'Pending' => 'heroicon-s-clock',
                                'Accepted' => 'heroicon-s-check-circle',
                                default => 'heroicon-s-x-circle',
                            })
                            ->color(fn ($state) => match ($state) {
                                'Pending' => 'warning',
                                'Accepted' => 'success',
                                default => 'danger',
                            })
                            ->visible(fn() => Auth::user()->role === 'Housekeeper'),
                    ]),
                ])->collapsible(),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
                'sm' => 3,
            ])
            ->filters([
                //
            ])
            ->actions([
                // Housekeeper can only edit their own request
                Tables\Actions\EditAction::make()
                    ->visible(fn() => Auth::user()->role === 'Housekeeper'),

                // Front desk can change the status to 'pending'
                Tables\Actions\Action::make('setPending')
                    ->label('Set as Pending')
                    ->action(function (MaintenanceRequest $record, $action) {
                        if ($record->status === 'Pending') {
                            Notification::make()->title('Already set to Pending!')->warning()->send();

                        } else {
                            $record->update(['status' => 'Pending']);
                            Notification::make()->title(title: 'Status set to Pending!')->success()->send();

                        }
                    })
                    ->hidden(condition: fn(): bool => Auth::user()->role === 'Housekeeper'),

                // Front desk can change the status to 'Accepted'
                Tables\Actions\Action::make('setAccepted')
                    ->label('Set as Accepted')
                    ->action(function (MaintenanceRequest $record, $action) {
                        if ($record->status === 'Accepted') {
                            Notification::make()->title('Already set to Accepted!')->warning()->send();

                        } else {
                            $record->update(['status' => 'Accepted']);
                            Notification::make()->title(title: 'Status set to Accepted!')->success()->send();
                        }
                    })
                    ->hidden(condition: fn(): bool => Auth::user()->role === 'Housekeeper'),
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
    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if ($user->role === 'FrontDesk' || $user->role === 'Manager') {

            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()
        
            ->where('user_id', $user->id);
    }
 
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaintenanceRequests::route('/'),
            'create' => Pages\CreateMaintenanceRequest::route('/create'),
            'edit' => Pages\EditMaintenanceRequest::route('/{record}/edit'),
        ];
    }
}
