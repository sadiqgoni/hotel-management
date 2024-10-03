<div class="max-w-full">
    <div class="text-center space-y-2">
        <h1 class="text-3xl font-semibold text-gray-700 dark:text-gray-200">{{ __('Meal Order Report') }}</h1>
        <h3 class="text-xl text-gray-700 dark:text-gray-200">{{ $header['restaurant_name'] }}</h3>
    </div>
    
    <p class="mb-4 text-gray-700 dark:text-gray-200">{{ __('Period') }}: 
        <b>{{ $header['start_date'] }} - {{ $header['end_date'] }}</b>
    </p>

    <x-table class="w-full table-fixed">
        <x-table-header>
            <x-table-row>
                <x-table-header-cell class="text-gray-700 dark:text-gray-300">@lang('Order ID')</x-table-header-cell>
                <x-table-header-cell class="text-gray-700 dark:text-gray-300">@lang('Menu Item Name')</x-table-header-cell>
                <x-table-header-cell class="text-gray-700 dark:text-gray-300">@lang('Quantity')</x-table-header-cell>
                <x-table-header-cell class="text-gray-700 dark:text-gray-300">@lang('Price')</x-table-header-cell>
                <x-table-header-cell class="text-gray-700 dark:text-gray-300">@lang('Total Price')</x-table-header-cell>
            </x-table-row>
        </x-table-header>

        <tbody>
            @foreach($orders as $order)
            <x-table-row class="hover:bg-gray-700 dark:hover:bg-gray-800 transition-colors duration-150 ease-in">
                <x-table-cell class="text-gray-800 dark:text-gray-200">{{ $order['order_id'] }}</x-table-cell>
                <x-table-cell class="text-gray-800 dark:text-gray-200">{{ $order['menu_item_name'] }}</x-table-cell>
                <x-table-cell class="number text-gray-800 dark:text-gray-200">{{ $order['quantity'] }}</x-table-cell>
                <x-table-cell class="number text-gray-800 dark:text-gray-200">{{ $order['price'] }}</x-table-cell>
                <x-table-cell class="number text-gray-800 dark:text-gray-200">{{ $order['total_price'] }}</x-table-cell>
            </x-table-row>
            @endforeach

            <x-table-row class="bg-gray-100 dark:bg-gray-800 font-bold">
                <x-table-cell colspan="4" class="text-gray-800 dark:text-gray-200">{{ __('Total') }}</x-table-cell>
                <x-table-cell class="number text-gray-800 dark:text-gray-200">{{ $footer['total_amount'] }}</x-table-cell>
            </x-table-row>
        </tbody>
    </x-table>
</div>
