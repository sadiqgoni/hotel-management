<?php

namespace App\Filament\Frontdesk\Resources;

use App\Filament\Frontdesk\Resources\GroupReservationResource\Pages;
use App\Filament\Frontdesk\Resources\Frontdesk\GroupReservationResource\RelationManagers;
use App\Models\GroupReservation;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Notifications\Collection;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Guest;
use App\Models\CouponManagement;

use App\Models\Room;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\SoftDeletes;

class GroupReservationResource extends Resource
{
    protected static ?string $model = GroupReservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Reservation Management';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Wizard::make([
                // Step 1: Organization Information
                Step::make('Organization Information')
                    ->icon('heroicon-o-building-office')
                    ->description('Enter organization and contact details.')
                    ->completedIcon('heroicon-m-check-circle')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('organization_name')
                            ->label('Organization Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('contact_person')
                            ->label('Primary Guest Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('contact_phone')
                            ->label('Primary Guest Number')
                            ->nullable()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('contact_email')
                            ->label('Primary Guest Email')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('group_size')
                            ->label('Group Size')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                    ]),

                // Step 2: Reservation Details
                Step::make('Reservation Details')
                    ->icon('heroicon-o-calendar')
                    ->description('Provide reservation details including rooms and dates.')
                    ->completedIcon('heroicon-m-check-circle')
                    ->columns(2)

                    ->schema([
                        Forms\Components\Select::make('room_ids')
                            ->label('Select Rooms')
                            ->multiple()
                            ->searchable()
                            ->options(
                                Room::where('status', 1)
                                    ->get()
                                    ->mapWithKeys(function ($room) {
                                        return [$room->id => "{$room->room_number} - ₦{$room->price_per_night}"];
                                    })
                                    ->toArray()
                            ) // Only available rooms with formatted price
                            ->placeholder('Choose rooms (e.g., DEL003 - ₦5000)')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $rooms = Room::whereIn('id', $state)->get();
                                $totalAmount = $rooms->sum('price_per_night');
                                $set('price_per_night', $totalAmount); // Sum of prices for all selected rooms
                                static::updateTotalAmount($get, $set);
                            }),
                        TextInput::make('price_per_night')
                            ->label('Total Price per Night')
                            ->readOnly(),

                        Forms\Components\DatePicker::make('check_in_date')
                            ->label('Check-In Date')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                static::updateTotalAmount($get, $set);
                                static::updateNumberOfNights($get, $set);
                            }),

                        Forms\Components\DatePicker::make('check_out_date')
                            ->label('Check-Out Date')
                            ->required()
                            ->afterOrEqual('check_in_date')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                static::updateTotalAmount($get, $set);
                                static::updateNumberOfNights($get, $set);
                            }),

                        Forms\Components\TextInput::make('number_of_nights')->readOnly()->label('Number of Nights'),
                    ]),

                // Step 3: Apply Discounts
                Step::make('Apply Discounts')
                    ->icon('heroicon-o-tag')
                    ->description('Apply any available discount coupons.')
                    ->completedIcon('heroicon-m-check-circle')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('coupon_management_id')
                            ->label('Apply Coupon')
                            ->searchable()
                            ->options(CouponManagement::where('status', 'active')->pluck('code', 'id')->toArray())
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $coupon = CouponManagement::find($state);
                                if ($coupon) {
                                    static::applyCoupon($coupon, $get, $set);
                                }
                            }),

                        Forms\Components\TextInput::make('discount_amount')->label('Discount Amount')->readOnly(),
                        Forms\Components\TextInput::make('total_amount')->label('Total Amount')->readOnly(),
                    ]),

                // Step 4: Payment Details
                Step::make('Payment Details')
                    ->icon('heroicon-o-credit-card')
                    ->description('Enter payment details and check payment status.')
                    ->completedIcon('heroicon-m-check-circle')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('payment_method')
                            ->label('Payment Method')
                            ->searchable()
                            ->options([
                                'card' => 'Card Payment',
                                'cash' => 'Cash Payment',
                                'mobile' => 'Mobile Transfer Payment',
                            ]),

                        Forms\Components\TextInput::make('amount_paid')
                            ->label('Amount Paid')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                static::checkPaymentStatus($get, $set);
                            }),

                        Forms\Components\TextInput::make('remaining_balance')->label('Remaining Balance')->readOnly(),
                        Forms\Components\TextInput::make('payment_status')
                            ->label('Payment Status')
                            ->readOnly(),
                    ]),

                // Step 5: Special Requests & Final Confirmation
                Step::make('Special Requests & Confirmation')
                    ->icon('heroicon-o-check')
                    ->description('Add any special requests and confirm the reservation.')
                    ->completedIcon('heroicon-m-check-circle')
                    ->schema([
                        Forms\Components\Textarea::make('special_requests')->label('Special Requests (Optional)'),
                        Forms\Components\Select::make('status')
                            ->label('Reservation Status')
                            ->searchable()
                            ->options([
                                'Confirmed' => 'Confirmed',
                                'On Hold' => 'On Hold',
                            ])
                            ->required(),
                    ]),
            ])->skippable()
                ->columnSpanFull()
        ]);
    }

    protected static function updateTotalAmount(callable $get, callable $set)
    {
        $checkInDate = Carbon::parse($get('check_in_date'));
        $checkOutDate = Carbon::parse($get('check_out_date'));

        // Use $get('room_ids') directly since it's already an array
        $rooms = Room::whereIn('id', $get('room_ids'))->get();

        // Calculate total amount based on room price and duration
        $totalAmount = $rooms->sum('price_per_night') * $checkInDate->diffInDays($checkOutDate);

        // Set the calculated total amount
        $set('total_amount', $totalAmount);
    }

    // protected static function updateTotalAmount(callable $get, callable $set)
    // {
    //     $checkInDate = Carbon::parse($get('check_in_date'));
    //     $checkOutDate = Carbon::parse($get('check_out_date'));
    //     $rooms = Room::whereIn('id', json_decode($get('room_ids'), true))->get();
    //     $pricePerNight = $rooms->avg('price_per_night');

    //     if ($checkInDate && $checkOutDate) {
    //         $days = $checkInDate->diffInDays($checkOutDate);
    //         $totalAmount = $days * $pricePerNight;

    //         // Apply coupon if available
    //         $discount = $get('discount_amount') ?? 0;
    //         $total = max(0, $totalAmount - $discount);

    //         $set('total_amount', $total);
    //     } else {
    //         $set('total_amount', 0);
    //     }
    // }

    public static function updateNumberOfNights(callable $get, callable $set)
    {
        $checkInDate = $get('check_in_date');
        $checkOutDate = $get('check_out_date');

        if ($checkInDate && $checkOutDate) {
            $checkIn = Carbon::parse($checkInDate);
            $checkOut = Carbon::parse($checkOutDate);

            $numberOfNights = $checkIn->diffInDays($checkOut);
            $set('number_of_nights', $numberOfNights);
        } else {
            $set('number_of_nights', 0);
        }
    }

    public static function applyCoupon($coupon, callable $get, callable $set)
    {
        $totalAmount = $get('total_amount');
        $discountAmount = 0;

        // Validate coupon application
        if ($coupon->discount_type === 'percentage') {
            $discountAmount = ($totalAmount * $coupon->discount_percentage) / 100;
        } elseif ($coupon->discount_type === 'fixed') {
            $discountAmount = min($totalAmount, $coupon->discount_amount);  // Prevent discount from exceeding total
        }

        $set('discount_amount', $discountAmount);
        static::updateTotalAmount($get, $set);
    }

    public static function checkPaymentStatus(callable $get, callable $set)
    {
        $totalAmount = $get('total_amount');
        $amountPaid = $get('amount_paid') ?? 0;

        // Validate payment to prevent overpaying
        if ($amountPaid > $totalAmount) {
            $set('payment_status', 'Overpayment detected');

            $amountPaid = $totalAmount;
        }



        // Update remaining balance
        $remainingBalance = $totalAmount - $amountPaid;
        $set('remaining_balance', $remainingBalance);

        // Update status based on remaining balance
        if ($remainingBalance == 0) {
            $set('payment_status', 'Full Payment');
        } else {
            $set('payment_status', 'Partial Payment');
        }
    }

    public static function table(Tables\Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                // Organization Information
                Tables\Columns\TextColumn::make('organization_name')
                    ->label('Organization')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contact_person')
                    ->label('Contact Person')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('group_size')
                    ->label('Group Size')
                    ->sortable(),

                // Rooms - display room numbers associated with the group

                Tables\Columns\TagsColumn::make('room_ids')
                    ->label('Rooms')
                    ->getStateUsing(function ($record) {
                        // Assuming room_ids is already an array of room IDs
                        return Room::whereIn('id', $record->room_ids)->pluck('room_number')->toArray();
                    }),


                // Status with a badge for better visualization
                BadgeColumn::make('status')
                    ->label('Status')
                    ->color(fn(string $state): string => match ($state) {
                        'Confirmed' => 'info',
                        'On Hold' => 'danger',
                        'Checked In' => 'success',
                        'Checked Out' => 'warning',
                    })
                    ->sortable(),
                // Dates of check-in and check-out
                Tables\Columns\TextColumn::make('check_in_date')
                    ->date()
                    ->label('Check-in Date'),
                Tables\Columns\TextColumn::make('check_out_date')
                    ->date()
                    ->label('Check-out Date'),
                // ->date('d M Y'),

                // Total Amount in a currency format
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money('NGN', true),
            ])
            ->filters([
                // Add filters for status, dates, or group size
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->searchable()
                    ->options([
                        'confirmed' => 'Confirmed',
                        'on Hold' => 'On Hold',
                        'checked In' => 'Checked In',
                        'checked Out' => 'Checked Out',
                    ]),

                Tables\Filters\Filter::make('check_in_date')
                    ->label('Check-in Date')
                    ->form([
                        Forms\Components\DatePicker::make('check_in_from')->label('From'),
                        Forms\Components\DatePicker::make('check_in_to')->label('To'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['check_in_from'], fn($query, $date) => $query->where('check_in_date', '>=', $date))
                            ->when($data['check_in_to'], fn($query, $date) => $query->where('check_in_date', '<=', $date));
                    }),

                // Tables\Filters\Filter::make('group_size')
                //     ->label('Group Size')
                //     ->query(fn(Builder $query) => $query->where('group_size', '>=', 5)), // Example filter for large groups
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                // Tables\Actions\BulkAction::make('confirm')
                //     ->label('Confirm Reservations')
                //     ->action(fn(Collection $records) => $records->each->update(['status' => 'confirmed']))
                //     ->requiresConfirmation(),
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
            'index' => Pages\ListGroupReservations::route('/'),
            'create' => Pages\CreateGroupReservation::route('/create'),
            'edit' => Pages\EditGroupReservation::route('/{record}/edit'),
        ];
    }
}

