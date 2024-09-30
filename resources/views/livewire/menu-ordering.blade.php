<div class="flex bg-platinum dark:bg-gray-900 dark:text-gray-100">
    {{-- Main Content Area --}}
    <div class="flex-grow p-6 h-screen overflow-y-auto">
        <!-- Search bar -->
        <div class="mb-4">
            <x-filament::input.wrapper suffix-icon="heroicon-m-magnifying-glass" suffix-icon-color="primary">
                <x-filament::input type="text" wire:model.live.debounce.300ms="searchTerm"
                    placeholder="Search menu items..." />
            </x-filament::input.wrapper>
        </div>

        {{-- Menu Categories --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-4">
            <x-filament::button outlined size="sm" color="coral" wire:click="showAllItems"
                class="p-2 bg-white dark:bg-gray-800 rounded-lg shadow-md hover:bg-black transition-all duration-300 {{ is_null($selectedCategory) ? 'ring-2 ring-primary-500' : '' }}">
                <div class="flex items-center gap-2">
                    <span class="text-3xl mb-2">🍽️</span> <!-- Default icon for "All Items" -->
                    <h3 class="font-bold text-sm">All Items</h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $totalItems }} items</p>
                </div>
            </x-filament::button>
            @foreach ($categories as $category)
                <x-filament::button outlined size="sm" color="coral" wire:click="filterByCategory({{ $category->id }})"
                    class="p-2 bg-white dark:bg-gray-800 rounded-lg shadow-md hover:bg-black transition-all duration-300 {{ $selectedCategory == $category->id ? 'ring-2 ring-primary-500' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="text-3xl mb-2">{{ $category->icon }}</span>
                        <h3 class="font-bold text-sm">{{ $category->name }}</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400">
                            {{ $category->menu_items_count ?? 'No' }}
                            {{ $category->menu_items_count == 1 ? 'item' : 'items' }}
                        </p>

                    </div>
                </x-filament::button>
            @endforeach
        </div>

        {{-- Menu Items Section --}}
        <h2 class="text-2xl font-bold mb-4 text-center">
            {{ $selectedCategoryName ?: 'All Menu Items' }}
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($menuItems as $item)
                <x-filament-tables::container
                    class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md transition-all duration-300 hover:shadow-xl">
                    <div>
                        <div class="flex-grow min-h-[200px]"> <!-- Set a minimum height -->
                            <img src="{{ $item->image ? asset('storage/' . $item->image) : asset('hotel2.png') }}"
                                alt="{{ $item->name }}" class="w-full h-32 rounded-md object-cover mb-4" />
                            <h4 class="font-bold text-lg text-center mb-2">{{ $item->name }}</h4>
                            <p class="text-sm text-gray-600 text-center mb-2">
                                {{ $item->description ?: 'No description available.' }}
                            </p> <!-- Default text -->
                            <p class="font-bold text-lg text-center mb-4"> ₦ {{ number_format($item->price, 2) }}</p>
                        </div>

                        <div class="flex items-center justify-center space-x-2 gap-2 mt-4">
                            <button wire:click="removeFromCart({{ $item->id }})"
                                class="px-2 py-1.6 bg-gray-300 dark:bg-black rounded-full text-gray-600 font-bold">
                                -
                            </button>
                            <span class="text-lg font-semibold">{{ $cartItems[$item->id]['quantity'] ?? 0 }}</span>
                            <button wire:click="addToCart({{ $item->id }})"
                                class="px-1.5 py-1.6 bg-primary-500 rounded-full text-white hover:bg-primary-600 transition-colors duration-300">
                                +
                            </button>
                        </div>
                    </div>

                </x-filament-tables::container>
            @endforeach

        </div>
    </div>

           

    {{-- Invoice Sidebar --}}
    <div class="w-1/4 min-w-[300px]">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg h-screen sticky top-0 overflow-y-auto">
            <h2 class="text-2xl font-bold mb-4">Invoice</h2>

            <!-- Invoice Items -->
            @if (count($cartItems) > 0)
                <div class="space-y-4 mb-4">
                    @foreach ($cartItems as $itemId => $cartItem)
                        <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                            <div>
                                <h4 class="font-semibold">{{ $cartItem['name'] }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">x{{ $cartItem['quantity'] }}</p>
                            </div>
                            <span class="font-bold"> ₦ {{ number_format($cartItem['price'] * $cartItem['quantity'], 2) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500 dark:text-gray-400 mb-4">Cart is empty</p>
            @endif

            <!-- Payment Summary -->
            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg mb-4">
                <h3 class="font-bold mb-2">Payment Summary</h3>
                <div class="flex justify-between mb-2">
                    <span>Subtotal</span>
                    <span> ₦ {{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span>Service Charge</span>
                    
                    <span> ₦ {{ number_format($tax, 2) }}</span>
                </div>
                <div
                    class="flex justify-between font-bold text-lg mt-2 pt-2 border-t border-gray-300 dark:border-gray-600">
                    <span>Total</span>
                    <span> ₦ {{ number_format($total, 2) }}</span>
                </div>
            </div>
            <x-filament::section icon="heroicon-o-user" icon-color="primary" collapsible class="mb-4" icon-size="lg">
                <x-slot name="heading">Customer Information</x-slot>
                {{ $this->form }}
            </x-filament::section>

            <x-filament::button wire:click="placeOrder" class="w-full mt-4" color="primary"
                :disabled="count($cartItems) === 0" icon="heroicon-m-sparkles">
                Place Order
            </x-filament::button>

            <button wire:click="addToCart({{ $item->id }})"
                x-on:keydown.window.prevent.ctrl.1="$wire.addToCart({{ $item->id }})">

        </div>
    </div>
</div>