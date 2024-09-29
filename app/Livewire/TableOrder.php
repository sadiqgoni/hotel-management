<?php 

namespace App\Livewire;

use Livewire\Component;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Models\Order;
use Filament\Forms\Concerns\InteractsWithForms;


class TableOrder extends Component
{
    public $orderId;
    public $paymentMethod;
    public $totalAmount;
    public $payableAmount;
    public $changeAmount;

    public function mount(Order $record)
    {
        // Initialize the modal with order data
        $this->orderId = $record->id;
        $this->totalAmount = $record->total_amount;
        $this->payableAmount = $this->totalAmount; // Default to total amount
        $this->calculateChange();
    }

    public function calculateChange()
    {
        $this->changeAmount = $this->payableAmount - $this->totalAmount;
    }

    public function submitPayment()
    {
        $order = Order::find($this->orderId);
        $order->payment_method = $this->paymentMethod;
        $order->paid_amount = $this->payableAmount;
        $order->change_amount = $this->changeAmount;
        $order->save();
        
        // Close the modal after processing the payment
        $this->dispatchBrowserEvent('close-payment-modal');
    }

    public function render()
    {
        return view('livewire.table-order', [
            'totalAmount' => $this->totalAmount,  // This is already part of the Livewire component
            'payableAmount' => $this->payableAmount,
            'changeAmount' => $this->changeAmount,
        ]);
    }
    
}

// class TableOrder extends Component
// {
//     public $orderId;
//     public $paymentMethod;
//     public $totalAmount = 0;
//     public $payableAmount = 0;
//     public $changeAmount = 0;

//     public function mount(Order $record)
//     {
//         // Initialize the modal with order data
//         $this->orderId = $record->id;
//         $this->totalAmount = $record->total_amount;
//         $this->payableAmount = $this->totalAmount; // Default to total amount
//         $this->calculateChange(); // Calculate changeAmount initially
//     }

//     public function calculateChange()
//     {
//         $this->changeAmount = $this->payableAmount - $this->totalAmount;
//     }


//     public function submitPayment()
//     {
//         $order = Order::find($this->orderId);
//         $order->payment_method = $this->paymentMethod;
//         $order->paid_amount = $this->payableAmount;
//         $order->change_amount = $this->changeAmount;
//         $order->save();        
//         // Close the modal after processing the payment
//         $this->dispatchBrowserEvent('close-payment-modal');
//     }

//     public function render()
//     {
//         return view('livewire.table-order',[
//             'changeAmount' => $this->changeAmount,
//         ]);
//     }
// }
