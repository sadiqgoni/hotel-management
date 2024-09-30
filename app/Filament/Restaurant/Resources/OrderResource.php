<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\OrderResource\Pages;
use App\Filament\Restaurant\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\ViewField;

use Filament\Tables\Actions\Modal\Actions\ButtonAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([

    //         ]);
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Invoice No.')->sortable()
                    ->formatStateUsing(function ($record) {
                        return '#' . $record->id;
                    }),
                Tables\Columns\TextColumn::make('customer_type')
                    ->label('Customer Name')
                    ->searchable()
                    ->formatStateUsing(function ($record) {
                        if ($record->customer_type === 'walkin') {
                            // Get the number of previous walk-in customers
                            $previousWalkinCount = $record::where('customer_type', 'walkin')
                                ->where('id', '<=', $record->id) // Adjust this to get all previous records
                                ->count();

                            // Return the customer number based on the count
                            return 'Customer 00' . str_pad($previousWalkinCount, 3, '0', STR_PAD_LEFT);
                        } elseif ($record->customer_type === 'guest' && $record->guest) {
                            return $record->guest->name;
                        }
                    }),
                Tables\Columns\TextColumn::make('dining_option')
                    ->label('Dining Option')
                    ->formatStateUsing(function ($record) {
                        // Check if the dining option is takeout
                        if ($record->dining_option === 'takeout') {
                            return 'Takeout';
                        }
                        // If dining in, show the table name
                        elseif ($record->dining_option === 'dinein' && $record->table) {
                            return $record->table->name; // Assuming 'name' is the table column
                        }
                    }),

                Tables\Columns\TextColumn::make('total_amount')->label('Total Amount')->money('NGN'),
                Tables\Columns\BadgeColumn::make('payment_method')
                    ->searchable()
                    ->colors([
                        'success' => 'cash',
                        'warning' => 'card',
                    ]),


                Tables\Columns\TextColumn::make('created_at')->label('Created At')->date(),

                // Tables\Columns\TextColumn::make('guest_id')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('user_id')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('table_id')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('subtotal')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('service_charge')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('total_amount')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('customer_type')
                //     ->searchable(),

                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])

            ->actions([

                Action::make('openPaymentModal')
                    ->label('Make Payment')
                    ->icon('heroicon-o-banknotes')
                    ->modalHeading('Make Payment')
                    ->modalContent(fn(Order $record) => view('filament.pages.order-table', ['order' => $record]))
                    ->color('success')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalWidth(\Filament\Support\Enums\MaxWidth::Medium),
                    // ->slideOver(),
                Action::make('generateInvoice')
                    ->label('Generate Invoice')
                    ->icon('heroicon-o-document-text')
                    ->url(fn(Order $record) => route('invoice.generate', $record))
                    ->openUrlInNewTab()
                    ->color('primary'),

                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),



                // ->form([

                //     TextInput::make('total_amount')
                //         ->label('total_amount'),          
                //               ])
                // ->action(function (array $data, Order $record): void {
                //     $record->$data['total_amount'];
                //     $record->save();
                // }),



                // Custom Action for the Payment Modal
                // Action::make('openPaymentModal')
                //     ->label('Make Payment')
                //     ->icon('heroicon-o-banknotes')
                //     ->modalHeading('Make Payment')
                //     ->modalWidth('lg')
                //     // ViewField::make('rating')
                //     // ->view('filament.pages.order-table'),
                //     ->modalContent(fn(Order $record) => view('livewire.table-order-component', ['record' => $record]))
                //     ->action(fn(Order $record) => null),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
