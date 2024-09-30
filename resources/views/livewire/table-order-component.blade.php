<div class="p-6 bg-white rounded-lg shadow-md">


    <h3 class="text-lg font-semibold mb-4">Payment for Invoice #{{ $order->id }}</h3>

    <!-- Total Amount Due -->
    <div class="mb-4">
        <h3 class="text-lg font-medium">Total Amount Due</h3>
        <p class="text-2xl text-green-600 font-bold">₦{{ number_format($order->total_amount, 2) }}</p>
    </div>

    <!-- Payment Form -->
    <form wire:submit.prevent="submit">
        <div class="mb-4">
            {{ $this->form }}
        </div>


        <!-- Payment Overview Table -->
        <table class="table-auto border-collapse w-full mt-4 mb-4">
            <thead>
                <tr>
                    <th class="border px-4 py-2">Description</th>
                    <th class="border px-4 py-2">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border px-4 py-2">Total Amount Due</td>
                    <td class="border px-4 py-2">₦{{ number_format($total_amount_due ?? $order->total_amount, 2) }}
                    </td>
                </tr>

                <tr>
                    <td class="border px-4 py-2">Service Charge</td>
                    <td class="border px-4 py-2">
                        @if(is_null($service_charge))
                            N/A
                        @else
                            ₦{{ number_format($service_charge ?? 0, 2) }}
                        @endif
                    </td>
                </tr>
                <!-- Total Amount (Updated with Service Charge) -->
                <tr>
                    <td class="border px-4 py-2">Total Amount (Updated)</td>
                    <td class="border px-4 py-2">
                        ₦{{ number_format($total_amount ?? $order->total_amount, 2) }}
                    </td>
                </tr>
                <tr>
                    <td class="border px-4 py-2">Change Due</td>
                    <td class="border px-4 py-2">₦{{ number_format($change_amount ?? 0, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <x-filament::button type="submit"  wire:click="submit" class="w-full mt-4" color="primary" icon="heroicon-m-sparkles">
            Process Payment </x-filament::button>
        
    </form>
</div>

    @script
    <script>
        $wire.on('closeModal', () => {
            $('#add').modal('hide');
        });
    </script>
    @endscript