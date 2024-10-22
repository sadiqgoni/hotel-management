<?php

namespace App\Filament\Frontdesk\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use App\Models\CheckIn;
use App\Models\CheckOut;
use Filament\Notifications\Notification;

class CheckOutPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    // Static properties
    protected static string $view = 'filament.frontdesk.pages.checkout';
    protected static ?string $navigationGroup = 'Daily Operations';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Check Out';
    protected static ?string $breadcrumb = 'Check Out Guest';
    protected static ?string $modelLabel = 'Check Out';
    protected static ?string $title = 'Check Out Guest';

    public ?array $data = [];
    public ?CheckIn $selectedCheckIn = null;
    public ?float $discountPercentage = 0;
    public ?float $discountAmount = 0;
    public ?float $additionalCharges = 0;
    public ?float $payableAmount = 0;
    public ?float $restaurantCharge = 0;
    public ?float $payingAmount = 0;
    public bool $showHistory = false;

    // Lifecycle method to initialize data
    public function mount()
    {
        $this->form->fill();
        $this->calculatePayableAmount();
    }

    // Toggle show/hide checkout history
    public function toggleHistory()
    {
        $this->showHistory = !$this->showHistory;
    }

    // Define the table of checked-out guests
    public function table(Table $table): Table
    {
        return $table->query(CheckOut::query())
            ->columns([
                TextColumn::make('guest_name')->label('Guest Name'),
                TextColumn::make('room_number')->label('Room Number'),
                TextColumn::make('check_in_time')->label('Check-in Date'),
                TextColumn::make('check_out_time')->label('Check-out Date'),
                TextColumn::make('total_amount')->label('Total Bill'),
            ])
            ->actions([

                Tables\Actions\Action::make('generateInvoice')
                ->label('Generate Invoice')
                ->icon('heroicon-o-document-text')
                ->url(fn(CheckOut $record) => route('invoiced.generate', ['id' => $record->id])) // Pass only the 'id'
                ->openUrlInNewTab()
                ->color('primary'),

            ]);
    }

    // Define the checkout form
    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('check_in_id')
                ->label('Select Guest and Room')
                ->options(
                    CheckIn::where('booking_status', 'Checked In')
                        ->pluck('room_number', 'id')
                        ->map(fn($room_number, $id) => "{$this->getGuestInfo($id, $room_number)}")
                )
                ->reactive()
                ->afterStateUpdated(fn($state) => $this->updateSelectedCheckIn($state))
                ->searchable()
                ->placeholder('Select a guest by name or room')
                ->required(),
        ])
        ->columns(2)
        ->statePath(path: 'data');
    }

    // Helper function to get guest info
    private function getGuestInfo($id, $room_number)
    {
        $checkIn = CheckIn::find($id);
        return "{$checkIn->guest_name} - Room {$room_number}";
    }

    // Update the selected check-in details
    public function updateSelectedCheckIn($checkInId)
    {
        $this->selectedCheckIn = CheckIn::find($checkInId);
        $this->restaurantCharge = $this->selectedCheckIn->restaurant_bill ?? 0;
        $this->calculatePayableAmount();
    }

    // Update discount percentage and recalculate
    public function updatedDiscountPercentage()
    {
        $this->calculateDiscount();
        $this->calculatePayableAmount();
    }

    // Update additional charges and recalculate
    public function updatedAdditionalCharges()
    {
        $this->calculatePayableAmount();
    }

    // Calculate discount amount
    private function calculateDiscount()
    {
        $this->discountAmount = ($this->selectedCheckIn->total_amount ?? 0) * ($this->discountPercentage / 100);
    }

    // Calculate total payable amount
    private function calculatePayableAmount()
    {
        $totalAmount = $this->selectedCheckIn->total_amount ?? 0;
        $this->payableAmount = $totalAmount - $this->discountAmount + $this->additionalCharges;
    }

    // Perform the checkout action
    public function checkOut()
    {
        if (!$this->validateCheckOut()) {
            return;
        }

        // Create new checkout record
        $checkOut = $this->createCheckOutRecord();

        // Update the status of the check-in
        $this->updateCheckInStatus();

        // Notify user of success
        Notification::make()
            ->title('Checkout Successful')
            ->body('The guest has been successfully checked out.')
            ->success()
            ->send();
    }

    // Helper function to validate checkout
    private function validateCheckOut(): bool
    {
        if (!$this->selectedCheckIn) {
            $this->notifyError('No check-in selected', 'Please select a check-in to proceed with checkout.');
            return false;
        }

        $dueAmount = $this->payableAmount - $this->selectedCheckIn->paid_amount;
        if (($this->payingAmount ?? 0) > $dueAmount) {
            $this->notifyError('Insufficient Payment', "The payment must be at least ₦" . number_format($dueAmount, 2) . " to complete the checkout.");
            return false;
        }

        return true;
    }

    // Helper function to create the checkout record
    private function createCheckOutRecord()
    {
        return CheckOut::create([
            'check_in_id' => $this->selectedCheckIn->id,
            'guest_name' => $this->selectedCheckIn->guest_name,
            'room_number' => $this->selectedCheckIn->room_number,
            'check_in_time' => $this->selectedCheckIn->check_in_time,
            'check_out_time' => now(),
            'total_amount' => $this->payableAmount,
            'discount_percentage' => $this->discountPercentage,
            'discount_amount' => $this->discountAmount,
            'additional_charges' => $this->additionalCharges,
            'restaurant_charge' => $this->restaurantCharge,
            'paid_amount' => $this->selectedCheckIn->paid_amount + $this->payingAmount,
        ]);
    }

    // Helper function to update the status of the check-in
    private function updateCheckInStatus()
    {
        $this->selectedCheckIn->update(['booking_status' => 'Checked_out']);
    }

    // Helper function to show error notifications
    private function notifyError($title, $message)
    {
        Notification::make()
            ->title($title)
            ->body($message)
            ->danger()
            ->send();
    }
}
