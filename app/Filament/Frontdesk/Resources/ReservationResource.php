<?php

namespace App\Filament\Frontdesk\Resources;
use App\Filament\Frontdesk\Resources\ReservationResource\Pages;
use App\Models\CouponManagement;
use App\Models\Reservation;
use App\Models\Guest;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Actions\Action;

use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Jobs\ExpireReservation;

use Filament\Forms\Components\Section;

use Filament\Forms\Components\Card;

use Filament\Tables\Columns\IconColumn;

use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Mail;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Wizard::make([
                // Step 1: Guest Information
                Step::make('Guest Information')
                    ->icon('heroicon-o-user')  // Icon for user/guest
                    ->description('Enter guest details or select an existing guest.')
                    ->completedIcon('heroicon-m-check-circle')  // Completed icon
                    ->schema([
                        Select::make('guest_id')
                            ->label('Select Guest')
                            ->preload()
                            ->searchable()
                            ->options(Guest::pluck('name', 'id')->toArray())
                            ->placeholder('Select an existing guest or create a new one')
                            ->createOptionForm([
                                TextInput::make('name')->label('Full Name')->required()->maxLength(255),
                                TextInput::make('phone_number')->label('Phone Number')->unique(Guest::class, 'phone_number')->maxLength(255),
                                TextInput::make('nin_number')->label('NIN Number')->unique(Guest::class, 'nin_number')->maxLength(255),
                                Textarea::make('preferences')->label('Preferences')->placeholder('E.g., Halal food, quiet room'),
                            ])
                            ->createOptionAction(function (Action $action) {
                                return $action->modalHeading('Create New Guest')->modalButton('Add Guest')->modalWidth('lg');
                            })
                            ->createOptionUsing(function ($data) {
                                return Guest::create($data)->id;
                            }),
                    ]),

                // Step 2: Reservation Details
                Step::make('Reservation Details')
                    ->icon('heroicon-o-calendar')  // Icon for calendar/reservation
                    ->description('Provide reservation details including room and stay duration.')
                    ->completedIcon('heroicon-m-check-circle')  // Completed icon
                    ->columns(2)
                    ->schema([
                        Select::make('room_id')
                            ->label('Select Room')
                            ->searchable()
                            ->options(Room::all()->pluck('room_number', 'id')->toArray())
                            ->placeholder('Choose a room')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $room = Room::find($state);
                                if ($room) {
                                    $set('price_per_night', $room->price_per_night ?? 0);
                                    static::updateTotalAmount($get, $set);
                                }
                            }),
                    
                        TextInput::make('price_per_night')->label('Price per Night')->readOnly(),
                    
                        DatePicker::make('check_in_date')
                            ->label('Check-In Date')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                static::updateTotalAmount($get, $set);
                                static::updateNumberOfNights($get, $set);  // Update number of nights
                            }),
                    
                        DatePicker::make('check_out_date')
                            ->label('Check-Out Date')
                            ->required()
                            ->afterOrEqual('check_in_date')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                static::updateTotalAmount($get, $set);
                                static::updateNumberOfNights($get, $set);  // Update number of nights
                            }),
                    
                        TextInput::make('number_of_people')->label('Number of People')->required(),
                    
                        // New badge for displaying number of nights
                        TextInput::make('number_of_nights')
                        ->readOnly()
                            ->label('Number of Nights')
                            // ->icon('heroicon-o-calendar')  // You can choose any icon
                            // ->color('success')  // Change color based on UI preference
                            ->reactive()
                            // ->content(fn (callable $get) => 
                            //     $checkInDate = $get('check_in_date');
                            //     $checkOutDate = $get('check_out_date');
                            //     if ($checkInDate && $checkOutDate) {
                            //         $checkIn = \Carbon\Carbon::parse($checkInDate);
                            //         $checkOut = \Carbon\Carbon::parse($checkOutDate);
                            //         return $checkIn->diffInDays($checkOut) . ' Night(s)';
                            //     }
                            //     return 'Select dates';
                            // }),
                        ]),                    

                // Step 3: Apply Discounts
                Step::make('Apply Discounts')
                    ->icon('heroicon-o-tag')  // Icon for discount/coupon
                    ->description('Apply any available discount coupons.')
                    ->completedIcon('heroicon-m-check-circle')  // Completed icon

                    ->schema([
                        Select::make('coupon_management_id')->label('Apply Coupon')->searchable()->options(CouponManagement::where('status', 'active')->pluck('code', 'id')->toArray())->reactive()->afterStateUpdated(function ($state, callable $get, callable $set) {
                            $coupon = CouponManagement::find($state);
                            if ($coupon) {
                                static::applyCoupon($coupon, $get, $set);
                            }
                        }),
                        TextInput::make('discount_amount')->label('Discount Amount')->readOnly(),
                        TextInput::make('total_amount')->label('Total Amount')->readOnly(),
                    ]),

                // Step 4: Payment Details
                Step::make('Payment Details')
                    ->icon('heroicon-o-credit-card')  // Icon for payment/credit card
                    ->description('Enter payment details and check payment status.')
                    ->completedIcon('heroicon-m-check-circle')  // Completed icon

                    ->columns(2)

                    ->schema([
                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                'card' => 'Card Payment',
                                'cash' => 'Cash Payment',
                                'mobile' => 'Mobile Payment',
                            ])
                            ->required(),
                        TextInput::make('amount_paid')->label('Amount Paid')
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            static::checkPaymentStatus($get, $set);
                        }),
                        TextInput::make('remaining_balance')->label('Remaining Balance')->readOnly(),
                        TextInput::make('payment_status')->label('Payment Status')->readOnly(),
                    ]),

                // Step 5: Special Requests & Final Confirmation
                Step::make('Special Requests & Confirmation')
                    ->icon('heroicon-o-check')  // Icon for confirmation/special requests
                    ->description('Add any special requests and confirm the reservation.')
                    ->completedIcon('heroicon-m-check-circle')  // Completed icon

                    ->schema([
                        Textarea::make('special_requests')->label('Special Requests (Optional)'),
                        Select::make('status')->label('Reservation Status')->options([
                            'Confirmed' => 'Confirmed',
                            'On Hold' => 'On Hold',
                        ])->required(),
                    ]),
            ])->skippable()
                ->columnSpanFull()
        ]);
    }

    protected static function updateTotalAmount(callable $get, callable $set)
    {
        $checkInDate = Carbon::parse($get('check_in_date'));
        $checkOutDate = Carbon::parse($get('check_out_date'));

        if ($checkInDate && $checkOutDate) {
            $days = $checkInDate->diffInDays($checkOutDate);
            $pricePerNight = $get('price_per_night');
            $totalAmount = $days * $pricePerNight;

            // Apply coupon if available

            $discount = $get('discount_amount') ?? 0;
            $total = max(0, $totalAmount - $discount);  // Ensure total doesn't go below zero

            $set('total_amount', $total);
        } else {
            $set('total_amount', 0); // Reset total amount if invalid
        }
    }
    public static function updateNumberOfNights(callable $get, callable $set)
{
    $checkInDate = $get('check_in_date');
    $checkOutDate = $get('check_out_date');

    if ($checkInDate && $checkOutDate) {
        $checkIn = \Carbon\Carbon::parse($checkInDate);
        $checkOut = \Carbon\Carbon::parse($checkOutDate);

        $numberOfNights = $checkIn->diffInDays($checkOut);
        $set('number_of_nights', $numberOfNights);
    } else {
        $set('number_of_nights', 0); // Default to 0 if dates aren't set
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
            $amountPaid = $totalAmount;  // Prevent amount paid from exceeding total
        }

        $remainingBalance = $totalAmount - $amountPaid;
        $set('remaining_balance', $remainingBalance);

        // Mark the payment as partial or full
        if ($remainingBalance > 0) {
            $set('payment_status', 'Partial Payment');
        } else {
            $set('payment_status', 'Full Payment');
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('guest.name')
                    ->label('Guest Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('room.room_number')
                    ->label('Room Number')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('check_in_date')
                    ->label('Check-In Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('check_out_date')
                    ->label('Check-Out Date')
                    ->date()
                    ->sortable(),


                BadgeColumn::make('status')
                    ->label('Status')
                    ->color(fn(string $state): string => match ($state) {
                        'Confirmed' => 'info',
                        'On Hold' => 'danger',
                        'Checked In' => 'success',
                        'Checked Out' => 'warning',
                    })
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money('NGN', true)
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View Details')
                    ->icon('heroicon-o-eye')
                    // ->url(fn($record) => route('reservations.view', $record))
                    ->color('primary'),
                // Custom action for sending email
                Tables\Actions\Action::make('sendEmail')
                    ->icon('heroicon-o-envelope')
                    ->iconButton()
                    ->label('Send Email')
                    ->steps(fn($record) => static::getEmailWizard($record))
                    ->action(function ($data, $record) {
                        // Ensure guest and reservation details are available
                        $guest = $record->guest;
                        if (!$guest) {
                            Notification::make()
                                ->title('Error')
                                ->body('No guest associated with this reservation')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Prepare email and send it
                        $emailContent = $data['email_content'] ?? 'No message';
                        // Mail::to($guest->email)->send(new ReservationEmail($record, $emailContent));
            
                        Notification::make()
                            ->title('Email Sent')
                            ->body("Reservation details sent to {$guest->email}")
                            ->success()
                            ->send();
                    }),
                // Edit Action
                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil'),
                Tables\Actions\DeleteAction::make()->label('Delete'),
            ]);
    }

    // public static function updateTotalAmount(callable $get, callable $set)
    // {
    //     $checkIn = $get('check_in_date');
    //     $checkOut = $get('check_out_date');
    //     $roomPrice = $get('price_per_night');
    //     if ($checkIn && $checkOut && $roomPrice) {
    //         $days = Carbon::parse($checkOut)->diffInDays(Carbon::parse($checkIn));
    //         $total = $days * $roomPrice;
    //         $set('total_amount', $total);
    //     }
    // }
    public static function getEmailWizard($record): array
    {
        return [
            Step::make('Reservation Details')
                ->schema([
                    TextInput::make('guest_name')
                        ->default($record->guest->name)
                        ->disabled()
                        ->label('Guest Name'),

                    TextInput::make('room_number')
                        ->default($record->room->room_number)
                        ->disabled()
                        ->label('Room Number'),

                    TextInput::make('check_in_date')
                        ->default($record->check_in_date)
                        ->disabled()
                        ->label('Check-in Date'),

                    TextInput::make('check_out_date')
                        ->default($record->check_out_date)
                        ->disabled()
                        ->label('Check-out Date'),

                    TextInput::make('total_amount')
                        ->default($record->total_amount)
                        ->disabled()
                        ->label('Total Amount'),
                ]),

            Step::make('Email Message')
                ->schema([
                    RichEditor::make('email_content')
                        ->label('Custom Message')
                        ->placeholder('Write a custom message for the guest...')
                        ->required(),
                ]),

            Step::make('Confirmation')
                ->schema([
                    Checkbox::make('confirm_send')
                        ->label('Confirm sending this email?')
                        ->required(),
                ]),
        ];
    }

    public static function scheduleExpiration($reservationId)
    {
        ExpireReservation::dispatch($reservationId)->delay(now()->addHours(1));
    }

    // Wizard::make([
    //     // Step 1: Guest Information
    //     Step::make('Guest Information')
    //         ->schema([

    //             Select::make('guest_id')
    //                 ->label('Guest')
    //                 ->preload()
    //                 ->searchable()
    //                 ->options(Guest::pluck('name', 'id')->toArray())
    //                 ->required()
    //                 ->reactive()
    //                 ->createOptionForm([
    //                     // Fields for creating a new guest
    //                     TextInput::make('name')
    //                         ->label('Full Name')
    //                         ->required()
    //                         ->maxLength(255),

    //                     TextInput::make('phone_number')
    //                         ->label('Phone Number')
    //                         ->unique(Guest::class, 'phone_number')
    //                         ->maxLength(255),

    //                     TextInput::make('nin_number')
    //                         ->label('NIN Number')
    //                         ->unique(Guest::class, 'nin_number')
    //                         ->maxLength(255),

    //                     Textarea::make('preferences')
    //                         ->label('Preferences')
    //                         ->placeholder('Enter preferences (e.g., Halal food, quiet room)')
    //                 ])
    //                 ->createOptionAction(function (Action $action) {
    //                     return $action
    //                         ->modalHeading('Create Guest')
    //                         ->modalButton('Create Guest')
    //                         ->modalWidth('lg');
    //                 })
    //                 ->createOptionUsing(function ($data) {
    //                     // Logic for creating a new guest
    //                     $guest = Guest::create([
    //                         'name' => $data['name'],
    //                         'phone_number' => $data['phone_number'],
    //                         'nin_number' => $data['nin_number'],
    //                         'preferences' => $data['preferences'] ?? null,
    //                     ]);

    //                     return $guest->id;  // Return the newly created guest ID
    //                 }),
    //         ]),

    //     // Step 2: Room Selection
    //     Step::make('Room Selection')
    //         ->schema([
                // Select::make('room_id')
                //     ->label('Room')
                //     ->searchable()
                //     ->options(Room::all()->pluck('room_number', 'id')->toArray())
                //     ->required()
                //     ->placeholder('Select Room')
                //     ->reactive()
                //     ->afterStateUpdated(function ($state, callable $set) {
                //         $room = Room::find($state);
                //         $set('price_per_night', $room?->price_per_night ?? 0);
                //     }),

    //         ]),

    //     // Step 3: Reservation Details
    //     Step::make('Reservation Details')
    //         ->schema([

    //             DatePicker::make('check_in_date')
    //                 ->label('Check-In Date')
    //                 ->required()
    //                 ->reactive()
    //                 ->afterStateUpdated(function ($state, callable $get, callable $set) {
    //                     static::updateTotalAmount($get, $set);
    //                 }),

    //             DatePicker::make('check_out_date')
    //                 ->label('Check-Out Date')
    //                 ->required()
    //                 ->afterOrEqual('check_in_date')
    //                 ->reactive()
    //                 ->afterStateUpdated(function ($state, callable $get, callable $set) {
    //                     static::updateTotalAmount($get, $set);
    //                 }),
    //             TextInput::make('number_of_people')
    //                 ->label('Number of People')
    //                 ->required(),
    //             TextInput::make('price_per_night')
    //                 ->label('Price per Night')
    //                 ->readOnly(),
    // Textarea::make('special_requests')
    // ->label('Special Requests'),
    // Select::make('status')
    // ->label('Status')
    // ->options([
    //     'Confirmed' => 'Confirmed',
    //     'On Hold' => 'On Hold',
    // ])
    // ->required(),
    //         ]),

    // // Step 4: Payment & Invoice
    // Step::make('Payment & Invoice')
    //         ->schema([
    //                 Select::make('status')
    //                 ->label('Status')
    //                 ->options([
    //                     'Confirmed' => 'Confirmed',
    //                     'On Hold' => 'On Hold',
    //                 ])
    //                 ->required(),
    //             TextInput::make('total_amount')
    //                 ->label('Total Amount')
    //                 ->numeric()
    //                 ->readOnly()
    //                 ->placeholder('Auto-calculated based on Room Rate and Dates'),

    //         ]),
    // ])
    // ->fulls
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}

// namespace App\Filament\Frontdesk\Resources;

// use App\Filament\Frontdesk\Resources\ReservationResource\Pages;

// use App\Filament\Frontdesk\Resources\ReservationResource\RelationManagers;
// use App\Jobs\ExpireReservation;
// use App\Models\Reservation;
// use App\Models\Guest;
// use App\Models\Room;
// // use Filament\Forms\Components\Actions\Action;
// use Filament\Forms;
// use Filament\Forms\Components\RichEditor;
// use Filament\Tables\Actions\Action;

// use Filament\Forms\Components\Section;
// use Filament\Forms\Components\Wizard;
// use Filament\Forms\Components\Wizard\Step;
// use Filament\Notifications\Notification;
// use Filament\Resources\Resource;
// use Filament\Tables;
// use Filament\Forms\Components\Card;
// use Filament\Forms\Components\DatePicker;
// use Filament\Forms\Components\TextInput;
// use Filament\Forms\Components\Textarea;
// use Filament\Forms\Components\Select;
// use Filament\Tables\Columns\IconColumn;
// use Filament\Tables\Columns\TextColumn;
// use Filament\Tables\Columns\BadgeColumn;
// use Illuminate\Support\Carbon;
// use Illuminate\Validation\ValidationException;
//  class ReservationResource extends Resource
// {
//     protected static ?string $model = Reservation::class;
//     protected static ?string $navigationGroup = 'Operations Management';
//     protected static ?string $navigationIcon = 'heroicon-o-calendar';
//     protected static ?int $navigationSort = 2;



// public static function table(Tables\Table $table): Tables\Table
// {
//     return $table
//     ->columns([
//             TextColumn::make('id'),
//             // Tables\Columns\TextColumn::make('name')
//             // ->sortable()
//             // ->url(fn ($record) => route('filament.admin.resources.campaigns.index', ['record' => $record->id])),
//             Tables\Columns\TextColumn::make('guest.name'),
//             Tables\Columns\TextColumn::make('room.room_number'),
//             Tables\Columns\TextColumn::make('check_in_date'),
//             Tables\Columns\TextColumn::make('check_out_date')
//             ->label('Created On')->date()->toggleable()
//         ])
//         // columns([
//         //     TextColumn::make('id')->label('ID')->sortable()->searchable(),
//         //     TextColumn::make('guest.name')->label('Guest Name')->sortable()->searchable(),
//         //     TextColumn::make('room.room_number')->label('Room Number')->sortable()->searchable(),
//         //     TextColumn::make('check_in_date')->label('Check-In Date')->date()->sortable(),
//         //     TextColumn::make('check_out_date')->label('Check-Out Date')->date()->sortable(),
//         //     TextColumn::make('total_amount')->label('Total Amount')->sortable()->money('NGN'),

//         // ])
//             ->actions([
//                 Action::make('Print Invoice')
//                     ->icon('heroicon-o-printer')
//                     ->action(fn($record) => redirect()->route('reservations.invoice', ['reservation' => $record->id]))
//                     ->requiresConfirmation(),

//                     Action::make('Mail')
//                     ->icon('heroicon-m-envelope')
//                     ->iconButton()
//                     ->label('Send Email to Channels') 
//                     ->steps(fn ($record) => static::getWizardForm($record)),
//                 Tables\Actions\EditAction::make()->color('warning'),
//                 Tables\Actions\ViewAction::make()->color('success'),
//             ])
//             ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
//     }

// protected static function updateTotalAmount(callable $get, callable $set)
// {
//     $checkInDate = Carbon::parse($get('check_in_date'));
//     $checkOutDate = Carbon::parse($get('check_out_date'));

//     if ($checkInDate && $checkOutDate) {
//         $days = $checkInDate->diffInDays($checkOutDate);
//         $pricePerNight = $get('price_per_night');
//         $totalAmount = $days * $pricePerNight;

//         $set('total_amount', $totalAmount);
//     }
// }

//     protected static function getWizardForm($record): array
//     {

//         return [
//                     // Step::make('Select Channels')
//                     //     ->schema([
//                     //         Forms\Components\View::make('livewire-channel-table')
//                     //         ]),

//                     Step::make('Select Videos')
//                         ->schema([
//                             Forms\Components\View::make('livewire-video-table')
//                             ->viewData(['campaignId' => $record->id])
//                         ]),


//                     Step::make('Email Contents')
//                         ->schema([
//                             RichEditor::make('Email')->disableAllToolbarButtons()
//                             // ->default(settin::where('setting_key', 'email_message')->pluck('setting_value')->first()),

//                             // Fields for the third step
//                         ]),

//         ];
//     }

//     public static function scheduleExpiration($reservationId)
//     {
//         ExpireReservation::dispatch($reservationId)->delay(now()->addHours(1));
//     }

//     public static function getPages(): array
//     {
//         return [
//             'index' => Pages\ListReservations::route('/'),
//             'create' => Pages\CreateReservation::route('/create'),
//             'edit' => Pages\EditReservation::route('/{record}/edit'),
//         ];
//     }
// }


