<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\OrderResource\Pages;
use App\Filament\Restaurant\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Pages\Actions;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\DateFilter;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Actions\Action;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Sales';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('Invoice No.')->sortable(),
                BadgeColumn::make('status')
                    ->label('Order Status')

                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check-circle' => 'completed',
                        'heroicon-o-x-circle' => 'cancelled',
                    ])
                    ->colors([
                        'primary',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),
                    TextColumn::make('customer_type')
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
                    TextColumn::make('dining_option')
                    ->label('Dining Option')
                    ->formatStateUsing(function ($record) {
                        // Check if the dining option is takeout
                        if ($record->dining_option === 'takeout') {
                            return 'Takeout';
                        } 
                        // If dining in, show the table name
                        elseif ($record->dining_option === 'dinein' && $record->table) {
                            return  $record->table->name; // Assuming 'name' is the table column
                        }
                    }),
                
                TextColumn::make('total_amount')->label('Total Amount')->money('ngn'),
                TextColumn::make('created_at')->label('Created At')->date(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('customer_type')
                    ->options([
                        'walk-in' => 'Walk-In',
                        'hotel_guest' => 'Hotel Guest',
                    ]),
                // DateFilter::make('created_at')->label('Order Date'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
                // Action to open the payment modal
                Action::make('openPaymentModal')
                    ->label('Make Payment')
                    ->icon('heroicon-o-banknotes')
                    ->modalContent(fn (Order $record): View => view('livewire.table-order', ['record' => $record]))
                    ->action(fn (Order $record) => null),
                // Action::make('openPaymentModal')
                // ->label('Make Payment')
                // ->icon('heroicon-o-banknotes') 
                // ->modalContent(fn (Order $record): View => view('livewire.table-order', ['record' => $record]))
                // ->action(fn (Order $record) => $this->processPayment($record)) // Define what happens when the action is submitted
                // Action::make('Process Payment')
                //     ->icon('heroicon-o-credit-card')
                //     ->form([
                //         Forms\Components\Select::make('payment_method')
                //             ->label('Payment Method')
                //             ->options([
                //                 'cash' => 'Cash',
                //                 'credit_card' => 'Credit Card',
                //                 'mobile_payment' => 'Mobile Payment',
                //             ])
                //             ->required(),
                //         Forms\Components\TextInput::make('amount_paid')
                //             ->label('Amount Paid')
                //             ->numeric()
                //             ->required()
                //             ->reactive()
                //             ->afterStateUpdated(function ($state, callable $set, $get) {
                //                 // Calculate payable and change dynamically
                //                 $totalAmount = $get('total_amount');
                //                 $payableAmount = $totalAmount;
                //                 $changeAmount = max(0, $state - $totalAmount);
                                
                //                 $set('payable_amount', $payableAmount);
                //                 $set('change_amount', $changeAmount);
                //             }),
                //     ])
                //     ->action(function ($record, $data) {
                //         // Implement your logic here to process the payment
                //         $record->update([
                //             'payment_method' => $data['payment_method'],
                //             'paid_amount' => $data['amount_paid'],
                //         ]);
    
                //         Notification::make()
                //             ->title('Payment Processed')
                //             ->body('Payment successfully processed for Order ID: ' . $record->id)
                //             ->success()
                //             ->send();
                //     })
                //     ->modalHeading('Process Payment')
                //     ->modalSubheading('Please enter the payment details.')
                //     ->modalWidth('lg')
                //     ->extraModalActions([
                //         Tables\Actions\ButtonAction::make('Cancel')
                //             ->color('secondary')
                //             ->close(),
                //     ])
                    // ->form([
                    //     // Display Total, Payable, and Change amounts dynamically
                    //     Forms\Components\Card::make([
                    //         Forms\Components\TextInput::make('total_amount')
                    //             ->label('Total Amount')
                    //             ->numeric()
                    //             ->disabled()
                    //             ->default(fn ($record) => $record->total_amount),
                    //         Forms\Components\TextInput::make('payable_amount')
                    //             ->label('Payable Amount')
                    //             ->numeric()
                    //             ->disabled(),
                    //         Forms\Components\TextInput::make('change_amount')
                    //             ->label('Change Amount')
                    //             ->numeric()
                    //             ->disabled(),
                    //     ])->columns(1), // Ensure it displays in a column layout
                    // ]),
            ])
            // ->actions([
                
            //     Tables\Actions\ViewAction::make(),
            //     EditAction::make(),
            //     Tables\Actions\Action::make('Reprint Receipt')
            //         ->label('Reprint Receipt')
            //         ->icon('heroicon-o-printer')
            //         ->color('success')
            //         ->action(function ($record) {
            //             // Logic to handle receipt reprint
            //             Notification::make()
            //                 ->title('Receipt Reprint')
            //                 ->body('Reprinting receipt for Order ID: ' . $record->id)
            //                 ->success()
            //                 ->send();
            //         }),
            //     Action::make('Download Receipt')
            //         ->label('Download Receipt')
            //         ->icon('heroicon-o-arrow-down-tray')
            //         ->action(function ($record) {
            //             // Logic to generate and download the receipt PDF
            //             $filePath = 'receipts/order_' . $record->id . '.pdf'; // Assume it's stored in storage
            //             if (Storage::exists($filePath)) {
            //                 return Storage::download($filePath);
            //             } else {
            //                 Notification::make()
            //                     ->title('Receipt Not Found')
            //                     ->danger()
            //                     ->send();
            //             }
            //         }),
            //     Action::make('Update Status')
            //         ->label('Mark as Completed')
            //         ->color('primary')
            //         ->icon('heroicon-o-check')
            //         ->visible(fn($record) => $record->status === 'pending')
            //         ->action(function ($record) {
            //             $record->update(['status' => 'completed']);
            //             Notification::make()
            //                 ->title('Order Completed')
            //                 ->success()
            //                 ->send();
            //         }),
            // ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('Mark as Completed')
                        ->color('success')
                        ->icon('heroicon-o-check')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                $record->update(['status' => 'completed']);
                            }
                            Notification::make()
                                ->title('Orders Marked as Completed')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define relations here if needed (e.g., OrderItems, etc.)
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
