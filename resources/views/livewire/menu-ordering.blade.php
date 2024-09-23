<div class="flex bg-platinum dark:text-gray-400">
    {{-- Main Content Area --}}

    <div class="flex-grow p-4"  style="position: sticky; top: 0; height: 100vh; overflow-y: auto;">

        {{-- Menu Categories --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-4 ">

            {{-- MenuCategory components --}}
            @foreach ([['icon' => '🍞', 'title' => 'Breakfast', 'count' => 12], ['icon' => '🍔', 'title' => 'Lunch', 'count' => 12], ['icon' => '🍽️', 'title' => 'Dinner', 'count' => 12], ['icon' => '🥣', 'title' => 'Soup', 'count' => 12], ['icon' => '🍰', 'title' => 'Desserts', 'count' => 12], ['icon' => '🥗', 'title' => 'Side Dish', 'count' => 12], ['icon' => '🍤', 'title' => 'Appetizer', 'count' => 12], ['icon' => '🥤', 'title' => 'Beverages', 'count' => 12],] as $category)

                <x-filament-tables::container class="p-2">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl mr-2">{{ $category['icon'] }}</span>
                        <div>
                            <h3 class="font-bold">{{ $category['title'] }}</h3>
                            <p class="text-sm text-gray-600">{{ $category['count'] }} Menu In Stock</p>
                        </div>
                    </div>
                </x-filament-tables::container>
            @endforeach
        </div>

        {{-- Lunch Menu Section --}}
        <h2 class="text-2xl font-bold mb-6 text-center">Lunch Menu</h2>

        <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-3 gap-6">
            @foreach ([['name' => 'Pasta Bolognese', 'description' => 'Delicious beef lasagna with double chili Delicious beef', 'price' => 50.5, 'quantity' => 2], ['name' => 'Spicy Fried Chicken', 'description' => 'Delicious beef lasagna with double chili Delicious beef', 'price' => 45.7, 'quantity' => 2], ['name' => 'Grilled Steak', 'description' => 'Delicious beef lasagna with double chili Delicious beef', 'price' => 80.0, 'quantity' => 0], ['name' => 'Fish And Chips', 'description' => 'Delicious beef lasagna with double chili Delicious Beef', 'price' => 90.4, 'quantity' => 0], ['name' => 'Beef Bourguignon', 'description' => 'Delicious beef lasagna with double chili Delicious beef', 'price' => 75.5, 'quantity' => 0], ['name' => 'Spaghetti Carbonara', 'description' => 'Delicious beef lasagna with double chili Delicious beef', 'price' => 35.3, 'quantity' => 2], ['name' => 'Ratatouille', 'description' => 'Delicious beef lasagna with double chili Delicious beef', 'price' => 26.7, 'quantity' => 0], ['name' => 'Kimchi Jjigae', 'description' => 'Delicious beef lasagna with double chili Delicious beef', 'price' => 45.7, 'quantity' => 0], ['name' => 'Tofu Scramble', 'description' => 'Delicious beef lasagna with double chili Delicious beef', 'price' => 85.6, 'quantity' => 0]] as $item)

                <div class="bg-white p-4 rounded-lg shadow-md transition-transform transform hover:scale-105">
                    <!-- Image at the top -->
                    <img src="{{ asset('hotel2.png') }}" alt="{{ $item['name'] }}"
                        class="w-full h-32 rounded-md object-cover mb-4" />

                    <!-- Name -->
                    <h4 class="font-bold text-lg text-center mb-2">{{ $item['name'] }}</h4>

                    <!-- Description -->
                    <p class="text-sm text-gray-600 text-center mb-2">{{ $item['description'] }}</p>

                    <!-- Price -->
                    <p class="font-bold text-lg text-center mb-4">${{ number_format($item['price'], 2) }}</p>

                    <!-- Quantity and buttons -->
                    <div class="flex items-center justify-center space-x-2">
                        <button class="px-3 py-1 bg-gray-200 rounded-full text-gray-600 font-bold">-</button>
                        <span class="text-lg font-semibold">{{ $item['quantity'] }}</span>
                        <button class="px-3 py-1 bg-blue-500 text-white rounded-full">+</button>
                    </div>
                </div>

            @endforeach
        </div>


    </div>

    {{-- Invoice Sidebar --}}

    <x-filament-tables::container class="w-full">
    <div class="bg-gray-100 dark:bg-gray-800 dark:text-white p-4 rounded-lg shadow-md"
        style="position: sticky; top: 0; height: 50vh; overflow-y: auto;">
            <h2 class="text-2xl font-bold mb-4">Invoice</h2>
            <!-- <x-filament::loading-indicator class="p-6 text-primary-700 dark:text-primary-300"/> -->

            {{-- Dynamically Listed Invoice Items --}}
            <div class="divide-y divide-gray-300">
                @foreach ([['name' => 'Pasta Bolognese', 'price' => 50.5, 'quantity' => 2], ['name' => 'Spicy Fried Chicken', 'price' => 45.7, 'quantity' => 1]] as $invoiceItem)
                    <div class="flex justify-between py-2">
                        <div>
                            <h4 class="font-semibold">{{ $invoiceItem['name'] }}</h4>
                            <p class="text-sm text-gray-600">x{{ $invoiceItem['quantity'] }}</p>
                        </div>
                        <span
                            class="font-bold">${{ number_format($invoiceItem['price'] * $invoiceItem['quantity'], 2) }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Payment Summary --}}
            <div class="mt-8">
                <h3 class="font-bold">Payment Summary</h3>
                <div class="flex justify-between mt-2">
                    <span>Sub Total</span>
                    <span>${{ number_format(50.5 * 2 + 45.7, 2) }}</span> {{-- Dynamically calculate subtotal --}}
                </div>
                <div class="flex justify-between">
                    <span>Tax</span>
                    <span>$5.20</span> {{-- Tax (could also be calculated dynamically) --}}
                </div>
                <div class="flex justify-between font-bold mt-2">
                    <span>Total Payment</span>
                    <span>${{ number_format(50.5 * 2 + 45.7 + 5.2, 2) }}</span> {{-- Subtotal + Tax --}}
                </div>
            </div>
            {{-- Place Order Button --}}
            <x-filament::button color="primary"
                class="inline-flex  items-center rounded-lg  px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900"
                href="#" icon="heroicon-o-chevron-left" tag="a">
                Place An Order
            </x-filament::button>
        </div>
    </x-filament-tables::container>


</div>