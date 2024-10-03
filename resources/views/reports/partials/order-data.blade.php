<div class="max-w-full">
    <div class="text-center space-y-2">
        <h1 class="text-3xl font-semibold">{{ __('Meal Order Report') }}</h1>
        <h3 class="text-xl">{{ $header['restaurant_name'] }}</h3>
    </div>
    <p class="mb-4">{{ __('Period') }}: <b>{{ $header['start_date'] }} - {{ $header['end_date'] }}</b></p>

    <x-table class="w-full table-fixed">
        <x-table-header>
            <x-table-row>
                <x-table-header-cell>@lang('Order ID')</x-table-header-cell>
                <x-table-header-cell>@lang('Menu Item Name')</x-table-header-cell>
                <x-table-header-cell>@lang('Quantity')</x-table-header-cell>
                <x-table-header-cell>@lang('Price')</x-table-header-cell>
                <x-table-header-cell>@lang('Total Price')</x-table-header-cell>
            </x-table-row>
        </x-table-header>
        <tbody>
            @foreach($orders as $order)
            <x-table-row class="hover:bg-gray-100 transition-colors duration-150 ease-in">
            <x-table-cell>{{ $order['order_id'] }}</x-table-cell>
                    <x-table-cell>{{ $order['menu_item_name'] }}</x-table-cell> <!-- Display menu item name -->
                    <x-table-cell class="number">{{ $order['quantity'] }}</x-table-cell> <!-- Display quantity -->
                    <x-table-cell class="number">{{ $order['price'] }}</x-table-cell> <!-- Display price -->
                    <x-table-cell class="number">{{ $order['total_price'] }}</x-table-cell> <!-- Display total price -->
                </x-table-row>
            @endforeach



            <x-table-row class="bg-gray-100 font-bold">
                <x-table-cell colspan="4">{{ __('Total') }}</x-table-cell>

                <x-table-cell class="number">{{ $footer['total_amount'] }}</x-table-cell>
            </x-table-row>


         


        </tbody>
    </x-table>


</div>