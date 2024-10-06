<?php
namespace App\Filament\Frontdesk\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Infolist;
use App\Models\CheckIn;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\Log;
use App\Models\CheckOut;
use Filament\Notifications\Notification;


class CheckOutPage extends Page implements HasForms
{

    protected static string $view = 'filament.frontdesk.pages.checkout';
    protected static ?string $navigationGroup = 'Daily Operations';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Check Out';
    protected static ?string $breadcrumb =  'Check Out Guest';
    protected static ?string $modelLabel =  'Check Out';

    protected static ?string $title = 'Check Out Guest';
    
    use InteractsWithForms;

    public ?array $data = [];
    public ?CheckIn $selectedCheckIn = null;
    public ?float $discountPercentage = 0;
    public ?float $discountAmount = 0;
    public ?float $additionalCharges = 0;
    public ?float $payableAmount = 0;
    public ?float $restaurantCharge = 0;
    public ?float $payingAmount = 0;

    


    public function mount()
    {
        // When the component mounts, fill the form if needed.
        $this->form->fill();
        $this->calculatePayableAmount();  // Initialize the payable amount
    }
    
    

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('check_in_id')
                ->label('Select Guest and Room')
                ->options(CheckIn::query()->pluck('room_number', 'id')->map(function ($room_number, $id) {
                    $checkIn = CheckIn::find($id);
                    return "{$checkIn->guest_name} - Room {$room_number}";
                }))
                ->reactive()
                ->afterStateUpdated(fn($state) => $this->updateSelectedCheckIn($state))
                ->searchable()
                ->placeholder('Select a guest by name or room')
                ->required(),
        ])
            ->columns(2)
            ->statePath('data');
    }



    public function updateSelectedCheckIn($checkInId)
{
    $this->selectedCheckIn = CheckIn::find($checkInId);
    
    if ($this->selectedCheckIn) {
        $this->restaurantCharge = $this->selectedCheckIn->restaurant_bill; // Accessing restaurant_bill here
     
    } else {
        $this->restaurantCharge = 0; // Default value if selectedCheckIn is null
    }

    $this->calculatePayableAmount(); 
}
    // Real-time calculations
    public function updatedDiscountPercentage()
    {
        $this->calculateDiscount();
        $this->calculatePayableAmount();
    }

    public function updatedAdditionalCharges()
    {
        $this->calculatePayableAmount();
    }

    public function calculateDiscount()
    {
        $this->discountAmount = ($this->selectedCheckIn->total_amount ?? 0) * ($this->discountPercentage / 100);
    }

    public function calculatePayableAmount()
    {
        $totalAmount = $this->selectedCheckIn->total_amount ?? 0;
        $this->payableAmount = $totalAmount - $this->discountAmount + $this->additionalCharges;
    }
    
    
        // ... existing code ...
    
        public function checkOut()
        {
            // Validate that a check-in is selected
            if (!$this->selectedCheckIn) {
                Notification::make()
                    ->title('No check-in selected')
                    ->body('Please select a check-in to proceed with checkout.')
                    ->danger()
                    ->send();
                return;
            }
    
            // Calculate the due amount
            $dueAmount = $this->payableAmount - $this->selectedCheckIn->paid_amount;
    
            // Ensure the paying amount is equal to or greater than the due amount
            if (($this->payingAmount ?? 0) > $dueAmount) {
                Notification::make()
                    ->title('Insufficient Payment')
                    ->body("The payment amount must be at least ₦" . number_format($dueAmount, 2) . " to complete the checkout.")
                    ->danger()
                    ->send();
                return;
            }
    
            // Create a new CheckOut record
            $checkOut = new CheckOut();
            $checkOut->check_in_id = $this->selectedCheckIn->id;
            $checkOut->guest_name = $this->selectedCheckIn->guest_name;
            $checkOut->room_number = $this->selectedCheckIn->room_number;
            $checkOut->check_in_time = $this->selectedCheckIn->check_in_time;
            $checkOut->check_out_time = now(); // Current time as checkout time
            $checkOut->total_amount = $this->payableAmount;
            $checkOut->discount_percentage = $this->discountPercentage;
            $checkOut->discount_amount = $this->discountAmount;
            $checkOut->additional_charges = $this->additionalCharges;
            $checkOut->restaurant_charge = $this->restaurantCharge;
            $checkOut->paid_amount = $this->selectedCheckIn->paid_amount + $this->payingAmount;
            
            // Save the CheckOut record
            $checkOut->save();
    
            // Update the CheckIn record
            $this->selectedCheckIn->booking_status = 'Checked_out';
            $this->selectedCheckIn->save();
    
            // Show success notification
            Notification::make()
                ->title('Checkout Successful')
                ->body('The guest has been successfully checked out.')
                ->success()
                ->send();
    
            // Redirect or reset form as needed
            // $this->redirect(CheckOut::getUrl());
        }
    
        // ... rest of the existing code ...

}