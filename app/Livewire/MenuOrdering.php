<?php
namespace App\Livewire;

use App\Models\Guest;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use App\Models\Table;
use Filament\Notifications\Notification;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Attributes\Computed;

class MenuOrdering extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    public $menuItems = [];
    public $cartItems = [];
    public $subtotal = 0;
    public $tax = 0;
    public $total = 0;
    public $searchTerm = '';
    public $totalItems;

    public $customerType = '';
    public $selectedGuest = null;
    public $selectedTable = null;
    public $guestsWithRooms = [];
    public $tables = [];

    public $categories = [];
    public $selectedCategory = null;
    public $selectedCategoryName = '';

    public $diningOption = '';
    public $billingOption = '';
    public $paymentMethod = '';

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        // Load all categories with the item count
        $this->categories = MenuCategory::withCount('menuItems')->get();
        $this->menuItems = MenuItem::with('menuCategory')->get();
        $this->tables = Table::all();
        $this->totalItems = MenuItem::count(); // Total number of items
        $this->form->fill();

        $this->loadGuestsWithCheckedInReservations();

    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('customerType')
                    ->placeholder('Customer Type')
                    ->label('')
                    ->searchable()
                    ->options([
                        'walkin' => 'Walk-in Customer',
                        'guest' => 'Hotel Guest',
                    ])
                    ->reactive()
                    ->required(),

                Select::make('selectedGuest')
                    ->placeholder('Select Guest')
                    ->label('')
                    ->options(
                        Guest::query()
                            ->whereHas('reservations', function ($query) {
                                $query->where('status', 'Confirmed');  // Only show confirmed reservations
                            })
                            ->with('reservations')  // Eager load reservations
                            ->get()
                            ->flatMap(function ($guest) {
                                return $guest->reservations->map(function ($reservation) use ($guest) {
                                    return [
                                        $guest->id => "{$guest->name} - Room {$reservation->room->room_number}"
                                    ];
                                });
                            })
                            ->toArray()
                    )
                    ->searchable()
                    ->preload()
                    ->visible(fn($get) => $get('customerType') === 'guest')  // Only show when customer type is 'guest'
                    ->required(),  // Make it required


                Select::make('diningOption')
                    ->placeholder('Dining Option')
                    ->label('')
                    ->options([
                        'dinein' => 'Dine In',
                        'takeout' => 'Takeout',
                    ])
                    ->searchable()
                    ->reactive()
                    ->required(),

                Select::make('selectedTable')
                    ->searchable()
                    ->placeholder('Select Table')
                    ->label('')
                    ->options($this->tables->pluck('name', 'id'))
                    ->visible(fn($get) => $get('diningOption') === 'dinein'),

                Select::make('billingOption')
                    ->searchable()
                    ->placeholder('Billing Option')
                    ->label('')
                    ->options([
                        'charge_room' => 'Charge to Room',
                        'restaurant' => 'Settle in Restaurant',
                    ])
                    ->visible(fn($get) => $get('customerType') === 'guest'),

                Select::make('paymentMethod')
                    ->label('')
                    ->options([
                        'cash' => 'Cash',
                        'card' => 'Card',
                        'transfer' => 'Bank Transfer',
                    ])
                    ->placeholder('Payment Method')
                    ->searchable()
                    ->visible(fn($get) => $get('billingOption') === 'restaurant' || $get('customerType') === 'walkin'),
            ]);
    }

    public function loadGuestsWithCheckedInReservations()
    {
        $this->guestsWithRooms = Guest::whereHas('reservations', function ($query) {
            $query->where('status', 'Checked In');
        })->with([
                    'reservations' => function ($query) {
                        $query->where('status', 'Checked In')->with('room');
                    }
                ])->get();
    }
    public function updatedSearchTerm()
    {
        // Filter menu items based on search term
        $this->menuItems = MenuItem::where('name', 'like', '%' . $this->searchTerm . '%')->get();
    }



    public function filterByCategory($categoryId)
    {
        // Find the selected category
        $category = MenuCategory::find($categoryId);

        // Set the selected category ID and name
        $this->selectedCategory = $categoryId;
        $this->selectedCategoryName = $category->name;

        // Get the menu items for the selected category
        $this->menuItems = MenuItem::where('menu_category_id', $categoryId)->get();

        // Ensure the categories still have their menu_items_count
        $this->categories = MenuCategory::withCount('menuItems')->get();
    }

    public function showAllItems()
    {
        // Set selectedCategory to null to indicate "All Items"
        $this->selectedCategory = null;
        $this->selectedCategoryName = 'All Items';

        // Load all menu items
        $this->menuItems = MenuItem::all();
    }
    public function filteredMenuItems()
    {
        return MenuItem::with('menuCategory')
            ->where('name', 'like', '%' . $this->searchTerm . '%')
            ->when($this->selectedCategory, function ($query) {
                return $query->where('menu_category_id', $this->selectedCategory);
            })
            ->get();
    }

    public function addToCart($itemId)
    {
        $item = MenuItem::findOrFail($itemId);

        // Ensure that adding to the cart doesn't affect the displayed items
        if (isset($this->cartItems[$itemId])) {
            $this->cartItems[$itemId]['quantity']++;
        } else {
            $this->cartItems[$itemId] = [
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => 1,
            ];
        }

        $this->calculateTotal();
        $this->dispatch('cartUpdated');
    }


    public function removeFromCart($itemId)
    {
        if (isset($this->cartItems[$itemId])) {
            if ($this->cartItems[$itemId]['quantity'] > 1) {
                $this->cartItems[$itemId]['quantity']--;
            } else {
                unset($this->cartItems[$itemId]);
            }
        }

        $this->calculateTotal();
        $this->dispatch('cartUpdated');
    }

    public function calculateTotal()
    {
        $this->subtotal = collect($this->cartItems)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        $this->tax = $this->subtotal * 0.075;
        $this->total = $this->subtotal + $this->tax;
    }

    private function resetOrderState()
    {
        $this->reset([
            'cartItems',
            'subtotal',
            'tax',
            'total',
            'customerType',
            'selectedGuest',
            'selectedTable',
            'billingOption',
            'diningOption',
            'paymentMethod'
        ]);
    }



    public function placeOrder()
    {
        // try {
        //     // Validate using the existing validation rules
        //     $this->validate([
        //         'customerType' => 'required|in:guest,walkin',
        //         'diningOption' => 'required|in:dinein,takeout',
        //         'selectedTable' => 'required_if:diningOption,dinein',
        //         'selectedGuest' => 'required_if:customerType,guest',
        //         'billingOption' => 'required_if:customerType,guest|in:charge_room,restaurant',
        //         'paymentMethod' => 'required_if:billingOption,restaurant|required_if:customerType,walkin|in:cash,card,transfer',
        //     ], [
        //         'customerType.required' => 'Please select a customer type.',
        //         'diningOption.required' => 'Please select a dining option.',
        //         'selectedTable.required_if' => 'Please select a table for dine-in orders.',
        //         'selectedGuest.required_if' => 'Please select a guest for hotel guest orders.',
        //         'billingOption.required_if' => 'Please select a billing option for hotel guests.',
        //         'paymentMethod.required_if' => 'Please select a payment method.',
        //     ]);

            // Get data from the form
            $data = $this->form->getState();

            // Create the order
            $order = Order::create([
                'customer_type' => $data['customerType'],
                'guest_id' => $data['selectedGuest'],
                'table_id' => $data['selectedTable'],
                'total_amount' => $this->total,
                'payment_method' => $data['paymentMethod'], 
                'dining_option' => $data['diningOption'],
                'billing_option' => $data['billingOption'],
            ]);

            // Create order items
            foreach ($this->cartItems as $itemId => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $itemId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            // Show success notification
            Notification::make()
                ->title('Order Placed Successfully')
                ->body('Your order has been placed and is being processed.')
                ->success()
                ->send();

            // Reset order state
            $this->resetOrderState();

        // } catch (\Illuminate\Validation\ValidationException $e) {
        //     // Gather and display validation error messages
        //     $errors = $e->validator->errors()->all();
        //     Notification::make()
        //         ->title('Validation Errors')
        //         ->body(implode("\n", $errors))
        //         ->danger()
        //         ->send();
        // }
    }

    public function render()
    {
        return view('livewire.menu-ordering', [
            'menuItems' => $this->filteredMenuItems(),
            'cartItems' => $this->cartItems,
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'total' => $this->total,
            'categories' => $this->categories,
            'guestsWithRooms' => $this->guestsWithRooms,
            'totalItems' => $this->totalItems,
            'tables' => $this->tables,
        ]);
    }

}