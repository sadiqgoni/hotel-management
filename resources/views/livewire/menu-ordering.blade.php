<div class="flex bg-platinum dark:text-gray-400">

    {{-- Main Content Area --}}
    <div class="flex-grow p-4 h-screen overflow-y-auto">
        <!-- Search bar -->
        <x-filament-tables::container class="mb-4">
            <div>
                <input type="text" id="search" wire:model="searchTerm" placeholder="Search menu items..."
                    class="w-full p-3 border border-gray-100 rounded-lg shadow-sm focus:outline-none focus:ring focus:border-blue-300">
            </div>
        </x-filament-tables::container>

        {{-- Menu Categories --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-4">
            @foreach ($categories as $category)
                <x-filament-tables::container class="p-2 bg-white rounded-lg  cursor-pointer hover:bg-black transition"
                    wire:click="filterByCategory({{ $category->id }})">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl mr-2">{{ $category->icon }}</span>
                        <div>
                            <h3 class="font-bold">{{ $category->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $category->menu_items_count }} Menu In Stock</p>
                        </div>
                    </div>

                </x-filament-tables::container>
            @endforeach
        </div>

        {{-- Lunch Menu Section --}}
        <h2 class="text-2xl font-bold mb-4 text-center">{{ $selectedCategoryName ?? 'Restaurant Menu' }}</h2>

        <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-3 gap-6">
            @foreach ($menuItems as $item)
                <x-filament-tables::container
                    class="bg-white p-4 rounded-lg shadow-md transition-transform transform hover:scale-105">
                    <div>
                        <img src="{{ $item->image ? asset('storage/' . $item->image) : asset('hotel2.png') }}"
                            alt="{{ $item->name }}" class="w-full h-32 rounded-md object-cover mb-4" />


                        <h4 class="font-bold text-lg text-center mb-2">{{ $item->name }}</h4>
                        <p class="text-sm text-gray-600 text-center mb-2">{{ $item->description }}</p>
                        <p class="font-bold text-lg text-center mb-4">${{ number_format($item->price, 2) }}</p>

                        <div class="flex items-center justify-center space-x-2 gap-2">
                            <button wire:click="removeFromCart({{ $item->id }})"
                                class="px-2 py-1.6 bg-gray-300 dark:bg-black rounded-full text-gray-600 font-bold">-</button>
                            <span class="text-lg font-semibold">{{ $cartItems[$item->id]['quantity'] ?? 0 }}</span>
                            <button wire:click="addToCart({{ $item->id }})"
                                class="px-1.5 py-1.6 bg-gray-300 dark:bg-black rounded-full text-gray-600 font-bold">+</button>
                        </div>
                    </div>
                </x-filament-tables::container>
            @endforeach
        </div>
    </div>

    {{-- Invoice Sidebar --}}
    <x-filament-tables::container class="w-1/2">
        <div
            class="bg-gray-100 dark:bg-gray-800 dark:text-white p-4 rounded-lg shadow-md h-screen lg:h-auto lg:sticky lg:top-0 overflow-y-auto">
            <h2 class="text-2xl font-bold mb-4">Invoice</h2>

            <div class="divide-y divide-gray-300">
                @foreach ($cartItems as $cartItem)
                    <div class="flex justify-between py-2">
                        <div>
                            <h4 class="font-semibold">{{ $cartItem['name'] }}</h4>
                            <p class="text-sm text-gray-600 dark:text-white">x{{ $cartItem['quantity'] }}</p>
                        </div>
                        <span class="font-bold">${{ number_format($cartItem['price'] * $cartItem['quantity'], 2) }}</span>
                    </div>
                @endforeach
            </div>

            <div class="mt-2">
                <h3 class="font-bold">Payment Summary</h3>
                <div class="flex justify-between mt-2">
                    <span>Sub Total</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Tax</span>
                    <span>${{ number_format($tax, 2) }}</span>
                </div>
                <div class="flex justify-between font-bold mt-2 mb-4">
                    <span>Total Payment</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>
            </div>

            <x-filament::button class="w-full text-white py-2 mt-4 rounded-lg hover:bg-blue-600 transition">
                Place An Order
            </x-filament::button>
        </div>
    </x-filament-tables::container>
</div>