<div>
    <h3 class="text-lg font-semibold mb-4">Payment for Invoice #{{ $record->id }}</h3>
    <form wire:submit.prevent="submitPayment">
        {{-- Payment Method Selection --}}
        <x-filament::section icon="heroicon-o-currency-dollar" icon-color="primary" collapsible class="mb-4">
            <x-slot name="heading">Payment Method</x-slot>

            <x-filament::input.select wire:model="paymentMethod" class="mb-3">
                <option value="">Select Payment Method</option>
                <option value="cash">Cash</option>
                <option value="card">Credit Card</option>
                <option value="paypal">PayPal</option>
            </x-filament::input.select>
        </x-filament::section>

        {{-- Payment Information --}}
        <div class="mb-4">
            <label for="payableAmount" class="block text-sm font-medium text-gray-700">Total Amount</label>
            <input type="text" id="payableAmount" value="{{ $totalAmount }}" disabled class="mt-1 block w-full bg-gray-200 text-gray-900 border-gray-300 rounded-md shadow-sm sm:text-sm" />

            <label for="customerPayment" class="block text-sm font-medium text-gray-700 mt-4">Customer Payment</label>
            <input type="number" id="customerPayment" wire:model="payableAmount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm" placeholder="Enter payment amount" />
        </div>

        {{-- Change Amount --}}
        <div class="mb-4">
            <label for="changeAmount" class="block text-sm font-medium text-gray-700">Change Amount</label>
            <input type="text" id="changeAmount" value="{{ $changeAmount }}" disabled class="mt-1 block w-full bg-gray-200 text-gray-900 border-gray-300 rounded-md shadow-sm sm:text-sm" />
        </div>

        {{-- Submit Button --}}
        <div class="flex space-x-4">
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                Pay Now & Print Invoice
            </button>

            <button type="button" wire:click="addPaymentMethod" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                Add New Payment Method
            </button>
        </div>
    </form>
</div>
