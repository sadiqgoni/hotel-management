<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\OrderResource\Pages;
use App\Filament\Restaurant\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\User;
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

use Illuminate\Support\Str;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Sales';
    protected static ?string $label = 'Order'; 
    protected static ?string $pluralLabel = 'Orders'; 

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Invoice No.')
                    ->sortable()
                    ->formatStateUsing(fn ($record) => '#' . $record->id),
                Tables\Columns\TextColumn::make('customer_type')
                    ->label('Customer Name')
                    ->searchable()
                    ->formatStateUsing(function ($record) {
                        if ($record->customer_type === 'walkin') {
                            $previousWalkinCount = $record::where('customer_type', 'walkin')
                                ->where('id', '<=', $record->id)
                                ->count();
                            return 'Customer 00' . str_pad($previousWalkinCount, 3, '0', STR_PAD_LEFT);
                        } elseif ($record->customer_type === 'guest' && $record->guest) {
                            return $record->guest->name;
                        }
                        return 'Unknown Customer'; 
                    }),
                Tables\Columns\TextColumn::make('dining_option')
                    ->label('Dining Option')
                    ->formatStateUsing(function ($record) {
                        return $record->dining_option === 'takeout'
                            ? 'Takeout'
                            : ($record->table ? $record->table->name : 'Dine In');
                    }),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money('NGN'),
                Tables\Columns\BadgeColumn::make('payment_method')
                    ->label('Payment Method')
                    ->searchable()
                    ->colors([
                        'success' => 'transfer',
                        'warning' => 'card',
                        'info' =>'cash'
                    ]),
                Tables\Columns\TextColumn::make('user_id')
                    ->label('Cashier')
                    ->formatStateUsing(fn ($state) => User::find($state)?->name ?? 'Deleted User'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->date(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('customer_type')
                    ->options([
                        'walkin' => 'Walk-in',
                        'guest' => 'Guest',
                    ])
                    ->placeholder('Select Customer Type'),
                // Tables\Filters\DateFilter::make('created_at')
                //     ->label('Order Date'),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'card' => 'Card',
                        'transfer' => 'Transfer',
                    ])
                    ->placeholder('Payment Method'),
            ])
            ->actions([
                Tables\Actions\Action::make('openPaymentModal')
                    ->label('Make Payment')
                    ->icon('heroicon-o-banknotes')
                    ->modalContent(fn(Order $record) => view('filament.pages.order-table', ['order' => $record]))
                    ->color('success')
                    ->modalWidth(\Filament\Support\Enums\MaxWidth::Medium),
                Tables\Actions\Action::make('generateInvoice')
                    ->label('Generate Invoice')
                    ->icon('heroicon-o-document-text')
                    ->url(fn(Order $record) => route('invoice.generate', $record))
                    ->openUrlInNewTab()
                    ->color('primary'),
                // Tables\Actions\Action::make('sendInvoice')
                //     ->label('Send Invoice')
                //     // ->icon('heroicon-o-mail')
                //     ->action(fn (Order $record) => $this->sendInvoice($record))
                //     ->requiresConfirmation()
                //     ->color('secondary'),
                // Tables\Actions\Action::make('reprintOrderSlip')
                //     ->label('Reprint Order Slip')
                //     ->icon('heroicon-o-printer')
                //     ->action(fn (Order $record) => $this->reprintOrderSlip($record))
                //     ->color('warning'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    protected function sendInvoice(Order $order)
    {
        // Logic for sending the invoice via email
    }

    protected function reprintOrderSlip(Order $order)
    {
        // Logic for reprinting the order slip
    }
}


// class OrderResource extends Resource
// {
//     protected static ?string $model = Order::class;

//     protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
//     protected static ?string $navigationGroup = 'Sales';
 
//     public static function table(Table $table): Table
//     {
//         return $table

//             ->columns([
//                 Tables\Columns\TextColumn::make('id')
//                     ->label('Invoice No.')->sortable()
//                     ->formatStateUsing(function ($record) {
//                         return '#' . $record->id;
//                     }),
//                 Tables\Columns\TextColumn::make('customer_type')
//                     ->label('Customer Name')
//                     ->searchable()
//                     ->formatStateUsing(function ($record) {
//                         if ($record->customer_type === 'walkin') {
//                             // Get the number of previous walk-in customers
//                             $previousWalkinCount = $record::where('customer_type', 'walkin')
//                                 ->where('id', '<=', $record->id) // Adjust this to get all previous records
//                                 ->count();

//                             // Return the customer number based on the count
//                             return 'Customer 00' . str_pad($previousWalkinCount, 3, '0', STR_PAD_LEFT);
//                         } elseif ($record->customer_type === 'guest' && $record->guest) {
//                             return $record->guest->name;
//                         }
//                     }),
//                 Tables\Columns\TextColumn::make('dining_option')
//                     ->label('Dining Option')
//                     ->formatStateUsing(function ($record) {
//                         // Check if the dining option is takeout
//                         if ($record->dining_option === 'takeout') {
//                             return 'Takeout';
//                         }
//                         // If dining in, show the table name
//                         elseif ($record->dining_option === 'dinein' && $record->table) {
//                             return $record->table->name; // Assuming 'name' is the table column
//                         }
//                     }),

//                 Tables\Columns\TextColumn::make('total_amount')->label('Total Amount')->money('NGN'),
//                 Tables\Columns\BadgeColumn::make('payment_method')
//                     ->searchable()
//                     ->colors([
//                         'success' => 'cash',
//                         'warning' => 'card',
//                     ]),
//                 Tables\Columns\TextColumn::make('user_id')
//                     ->label('Cashier')
//                     ->formatStateUsing(function ($state) {
//                         $user = User::find($state);
//                         return $user ? $user->name : 'Deleted User';
//                     }),

//                 Tables\Columns\TextColumn::make('created_at')->label('Created At')->date(),

//             ])->defaultSort('created_at', 'desc') // Use defaultSort method to sort by created_at in descending order

//             ->filters([
//                 //
//             ])

//             ->actions([

//                 Action::make('openPaymentModal')
//                     ->label('Make Payment')
//                     ->icon('heroicon-o-banknotes')
//                     ->modalHeading('Make Payment')
//                     ->modalContent(fn(Order $record) => view('filament.pages.order-table', ['order' => $record]))
//                     ->color('success')
//                     ->modalSubmitAction(false)
//                     ->modalCancelAction(false)
//                     ->modalWidth(\Filament\Support\Enums\MaxWidth::Medium),
//                 Action::make('generateInvoice')
//                     ->label('Generate Invoice')
//                     ->icon('heroicon-o-document-text')
//                     ->url(fn(Order $record) => route('invoice.generate', $record))
//                     ->openUrlInNewTab()
//                     ->color('primary'),
//                 Tables\Actions\EditAction::make(),
//                 Tables\Actions\ViewAction::make(),
//             ])

//             ->bulkActions([
//                 Tables\Actions\BulkActionGroup::make([
//                     Tables\Actions\DeleteBulkAction::make(),
//                 ]),
//             ]);
//     }
//     public static function getPages(): array
//     {
//         return [
//             'index' => Pages\ListOrders::route('/'),
//             'create' => Pages\CreateOrder::route('/create'),
//             'edit' => Pages\EditOrder::route('/{record}/edit'),
//         ];
//     }
// }
