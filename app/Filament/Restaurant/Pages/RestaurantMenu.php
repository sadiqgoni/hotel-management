<?php

namespace App\Filament\Restaurant\Pages;
use App\Models\Guest;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class RestaurantMenu extends Page implements HasForms
{
    use InteractsWithForms;

    // Navigation and view settings
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static string $view = 'filament.restaurant.pages.restaurant-menu';

    public ?array $data = [];  // Stores the form input data
    public $menuItems = [];
    public $cartItems = [];
    public $subtotal = 0.0;
    public $tax = 0.0;
    public $total = 0.0;
    public $searchTerm = '';
    public $totalItems = 0;

    public $customerType = '';
    public $selectedGuest = '';
    public $selectedTable = null;
    public $guestsWithRooms = [];
    public $tables = [];

    public $categories = [];
    public $selectedCategory = null;
    public $selectedCategoryName = 'All Items';

    // Dining and billing options
    public $diningOption = '';
    public $billingOption = '';
    public $paymentMethod = '';

    // Event listeners
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
    /**
     * Load guests who have checked-in reservations.
     */
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

    /**
     * Update menu items based on the search term.
     */
    public function updatedSearchTerm(): void
    {
        $this->menuItems = MenuItem::with('menuCategory')
            ->where('name', 'like', '%' . $this->searchTerm . '%')
            ->get()
            ->toArray();
    }

    public function filterByCategory(int $categoryId): void
    {
        $this->selectedCategory = $categoryId;
        $category = MenuCategory::findOrFail($categoryId);
        $this->selectedCategoryName = $category->name;

        // Fetch the menu items for the selected category
        $this->menuItems = MenuItem::with('menuCategory')
            ->where('menu_category_id', $categoryId)
            ->get(); // Keep as a collection for reactivity

        // Refresh categories with updated menu items count
        $this->categories = MenuCategory::withCount('menuItems')->get(); // Keep as a collection for reactivity
    }

    public function showAllItems(): void
    {
        $this->selectedCategory = null;
        $this->selectedCategoryName = 'All Items';
        $this->menuItems = MenuItem::with('menuCategory')->get()->toArray();
    }

    // public function filteredMenuItems()
    // {
    //     return MenuItem::with('menuCategory')
    //         ->where('name', 'like', '%' . $this->searchTerm . '%')
    //         ->when($this->selectedCategory, function ($query) {
    //             return $query->where('menu_category_id', $this->selectedCategory);
    //         })
    //         ->get();
    // }

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

        $this->tax = $this->subtotal * 0.0;
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
            'paymentMethod',
            'searchTerm',
            'selectedCategory',
            'selectedCategoryName',
        ]);
    }
    public function placeOrder()
    {
        // Validate that the required fields are filled based on customerType and diningOption
        if (!$this->customerType) {
            Notification::make()
                ->title('Missing Customer Type')
                ->body('Please select a customer type before placing an order.')
                ->warning()
                ->send();
            return;
        }

        if ($this->customerType === 'walkin') {
            if (!$this->diningOption) {
                Notification::make()
                    ->title('Missing Dining Option')
                    ->body('Please select a dining option for the walk-in customer.')
                    ->warning()
                    ->send();
                return;
            }

            if ($this->diningOption === 'dinein' && !$this->selectedTable) {
                Notification::make()
                    ->title('Missing Table Selection')
                    ->body('Please select a table for dine-in customers.')
                    ->danger()
                    ->send();
                return;
            }
            if (!$this->paymentMethod) {
                Notification::make()
                    ->title('Missing Payment Method')
                    ->body('Please select a payment method before placing the order.')
                    ->warning()
                    ->send();
                return;
            }
        }

        if ($this->customerType === 'guest') {
            if (!$this->selectedGuest) {
                Notification::make()
                    ->title('Missing Guest Selection')
                    ->body('Please select a guest with a confirmed reservation.')
                    ->warning()
                    ->send();
                return;
            }

            if (!$this->diningOption) {
                Notification::make()
                    ->title('Missing Dining Option')
                    ->body('Please select a dining option for the guest.')
                    ->warning()
                    ->send();
                return;
            }

            if ($this->diningOption === 'dinein' && !$this->selectedTable) {
                Notification::make()
                    ->title('Missing Table Selection')
                    ->body('Please select a table for the dine-in guest.')
                    ->danger()
                    ->send();
                return;
            }

            if ($this->diningOption === 'takeout' && !$this->billingOption) {
                Notification::make()
                    ->title('Missing Billing Option')
                    ->body('Please select a billing option for takeout (charge to room or settle in restaurant).')
                    ->warning()
                    ->send();
                return;
            }

            if ($this->billingOption === 'restaurant' && !$this->paymentMethod) {
                Notification::make()
                    ->title('Missing Payment Method')
                    ->body('Please select a payment method for the guest.')
                    ->warning()
                    ->send();
                return;
            }
        }

        // Proceed with placing the order after validation
        $data = $this->form->getState();

        $order = Order::create([
            'user_id' => auth()->id(),
            'customer_type' => $data['customerType'] ?? null,
            'guest_id' => $data['selectedGuest'] ?? null,
            'table_id' => $data['selectedTable'] ?? null,
            'total_amount' => $this->total ?? null,
            'payment_method' => $data['paymentMethod'] ?? null,
            'dining_option' => $data['diningOption'] ?? null,
            'billing_option' => $data['billingOption'] ?? null,
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

        // Reset order state after successful order placement
        $this->resetOrderState();

        // Redirect to the orders page

        return redirect('/restaurant/orders');

    }
}
