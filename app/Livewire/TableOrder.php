<?php
namespace App\Http\Livewire;

use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;

use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Contracts\View\View;

use Filament\Forms\Concerns\InteractsWithForms;

class TableOrder extends Component implements HasTable, HasForms
{
    use InteractsWithForms;
    use InteractsWithTable;
    public ?array $data = [];
    public $paymentMethod;
    public $totalAmount;
    public $amountPaid;
    public $amountChange = 0;

    public function mount(Order $order): void
    {
        $this->form->fill($order->toArray());

        // Load the total amount and other necessary data from the record
        // $this->totalAmount = $record->total_amount;
    }

    public function updatedAmountPaid()
    {
        $this->amountChange = $this->totalAmount - $this->amountPaid;
    }

    public function create(): void
    {
        dd($this->form->getState());

        // Handle the payment submission logic here
        // For example, saving the payment details to the database
        // You can emit an event or redirect after successful payment
    }


    public function render(): View
    {
        return view('livewire.table-order');
    }
    public function table(Table $table): Table
    {
        return $table->
        query(Order::query()->latest())->columns([
            TextColumn::make('created_at')->date()->size('sm'),
       
        ]);
    }

    // public function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //            Select::make('paymentMethod')
    //                 ->label('Payment Method')
    //                 ->options([
    //                     'cash' => 'Cash',
    //                     'card' => 'Card',
    //                 ])
    //                 ->required(),
                
    //             TextInput::make('totalAmount')
    //                 ->label('Total Amount')
    //                 ->disabled()
    //                 ->numeric()
    //                 ->required(),

    //            TextInput::make('amountPaid')
    //                 ->label('Amount Paid')
    //                 ->numeric()
    //                 ->required()
    //                 ->reactive()
    //                 ->afterStateUpdated(fn (callable $set) => $set('amountChange', $this->totalAmount - $this->amountPaid)),

    //          TextInput::make('amountChange')
    //                 ->label('Amount Change')
    //                 ->numeric()
    //                 ->disabled(),
    //         ])
    //         ->statePath('data');
    // }
}
