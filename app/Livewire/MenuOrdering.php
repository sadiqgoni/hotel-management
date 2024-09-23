<?php
namespace App\Livewire;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Notifications\Notification;
use Livewire\Component;

class MenuOrdering extends Component
{
    public $menuItems = [];
    public $cartItems = [];
    public $categories = [];
    public $selectedCategory = null;
    public $selectedCategoryName = null;
    public $searchTerm = '';
    public $subtotal = 0;
    public $tax = 0;
    public $total = 0;

    public function mount()
    {
        $this->categories = MenuCategory::all();
        $this->menuItems = MenuItem::all();
    }

    public function updatedSearchTerm()
    {
        // Filter menu items based on search term
        $this->menuItems = MenuItem::where('name', 'like', '%' . $this->searchTerm . '%')->get();
    }


    public function filterByCategory($categoryId)
    {
        $category = MenuCategory::find($categoryId);
        $this->selectedCategory = $categoryId;
        $this->selectedCategoryName = $category->name;
        $this->menuItems = MenuItem::where('menu_category_id', $categoryId)->get();
    }

    public function addToCart($itemId)
    {
        $item = MenuItem::find($itemId);

        if (isset($this->cartItems[$itemId])) {
            $this->cartItems[$itemId]['quantity']++;
        } else {
            $this->cartItems[$itemId] = [
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => 1,
                'total' => $item->price,
            ];
        }

        $this->calculateTotal();
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
    }

    public function calculateTotal()
    {
        $this->subtotal = array_reduce($this->cartItems, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        $this->tax = $this->subtotal * 0.1; // Example 10% tax
        $this->total = $this->subtotal + $this->tax;
    }

    public function placeOrder()
    {
        // Ensure cart has items
        if (empty($this->cartItems)) {
            Notification::make()->title('Cart is empty.')->danger()->send();
            return;
        }

        // Create the order
        $order = Order::create([
            'user_id' => auth()->id(), 
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'total_amount' => $this->total,
        ]);

        // Create order items
        foreach ($this->cartItems as $itemId => $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $itemId,
                'quantity' => $cartItem['quantity'],
                'price' => $cartItem['price'],
            ]);
        }

        // Clear the cart
        $this->cartItems = [];
        $this->subtotal = 0;
        $this->tax = 0;
        $this->total = 0;
        Notification::make()->title('Order has been placed successfully!')->success()->send();

    }


    public function render()
    {
        return view('livewire.menu-ordering');
    }
}
