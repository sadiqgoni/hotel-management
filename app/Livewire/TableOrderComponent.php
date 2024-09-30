<?php

namespace App\Livewire;
use App\Models\Order;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Filament\Notifications\Notification;
use Livewire\Component;
use Illuminate\Support\Facades\Log;


class TableOrderComponent extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    public ?string $payment_method = null;
    public ?float $amount_paid = null;
    public ?float $service_charge = null;
    public ?float $total_amount = null;
    public ?float $change_amount = 0.00;
    public Order $order;

    public function mount(Order $order): void
    {
        $this->order = $order;

        // Initialize the form state with order data
        $this->form->fill([
            'payment_method' => $order->payment_method,
            'amount_paid' => $order->amount_paid,
            'service_charge' => $order->service_charge,
        ]);

        $this->total_amount = $order->total_amount;
        $this->calculateChange();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'cash' => 'Cash',
                        'card' => 'Card',
                        'mobile_transfer' => 'Mobile Transfer',
                    ])
                    ->required(),

                TextInput::make('amount_paid')
                    ->label('Amount Paid')
                    ->numeric()
                    ->reactive()
                    ->required()
                    ->afterStateUpdated(fn($state) => $this->calculateChange()),

                TextInput::make('service_charge')
                    ->label('Service Charge')
                    ->numeric()
                    ->reactive()
                    ->afterStateUpdated(fn($state) => $this->calculateChange()),
            ])
            ->statePath('data');
    }

    public function calculateChange(): void
    {
        $serviceCharge = (float) ($this->data['service_charge'] ?? 0.0);
        $amountPaid = (float) ($this->data['amount_paid'] ?? 0.0);
        $this->total_amount = (float) $this->order->total_amount + $serviceCharge;
        $this->change_amount = $amountPaid - $this->total_amount;
    }

    public function submit(): void
    {
        // Update the order with the new payment data
        $this->order->payment_method = $this->data['payment_method'] ?? $this->order->payment_method;
        $this->order->amount_paid = $this->data['amount_paid'] ?? $this->order->amount_paid;
        $this->order->service_charge = $this->data['service_charge'] ?? $this->order->service_charge;

        // Calculate the new total amount
        $this->order->total_amount = $this->total_amount;

        // Save the order to the database
        $this->order->save();

        // Optionally, emit an event or flash message to indicate success
        Notification::make()
            ->title('Payment Placed Successfully')
            ->body('Payment successfully processed!')
            ->success()
            ->send();

        // Close the modal after saving

        // Emit event to close the modal
        $this->dispatch('closeModal');
    }

    public function render()
    {
        return view('livewire.table-order-component', [
            'order' => $this->order,
        ]);
    }
}


