<x-filament-panels::page>
    <x-filament-panels::form id="form" wire:key="{{ 'forms.' . $this->getFormStatePath() }}">
        {{ $this->form }}
    </x-filament-panels::form>

    {{-- Display the selected check-in details --}}
    @if ($selectedCheckIn)
        <div class="max-w-7xl ">
            <!-- Top Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <x-filament::section class="mb-4" icon="heroicon-m-user" icon-color="info" >
                    <x-slot name="heading">
                        Guest Details
                    </x-slot>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="font-medium">Guest Name:</span>
                            <span> {{ $selectedCheckIn->guest_name }} </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Room Number:</span>
                            <span> {{ $selectedCheckIn->room_number }} </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Reservation ID:</span>
                            <span> #000{{ $selectedCheckIn->reservation_number }} </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="font-medium">Mobile No:</span>
                            <span> {{ $selectedCheckIn->guest_phone }} </span>
                        </div>

                    </div>

                </x-filament::section>
                <!-- Add Reservation Summary Below -->
                <x-filament::section class="mb-4" icon="heroicon-m-document-text" icon-color="info">
                    <x-slot name="heading">
                        Reservation Summary
                    </x-slot>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="font-medium">Check-in Date:</span>
                            <span>{{ $selectedCheckIn->check_in_time }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Check-out Date:</span>
                            <span>{{ $selectedCheckIn->check_out_time }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Total Days:</span>
                            <span>{{ \Carbon\Carbon::parse($selectedCheckIn->check_in_time)->diffInDays($selectedCheckIn->check_out_time) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="font-medium">Late Check-out:</span>
                            <span>{{ $selectedCheckIn->late_check_out ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>
                </x-filament::section>

            </div>

            <div class="mb-4">
                <h2 class="text-lg font-semibold mb-4">Room Details</h2>
                <div class="overflow-x-auto">
                    <x-table class="w-full table-fixed border border-gray-200 shadow-md">
                        <!-- Table Header -->
                        <x-table-header class="bg-gray-100 dark:bg-gray-800">
                            <x-table-row>
                                <x-table-header-cell class="text-gray-700 dark:text-gray-300 font-semibold p-4">
                                    @lang('Room No.')
                                </x-table-header-cell>
                                <x-table-header-cell class="text-gray-700 dark:text-gray-300 font-semibold p-4">
                                    @lang('From Date')
                                </x-table-header-cell>
                                <x-table-header-cell class="text-gray-700 dark:text-gray-300 font-semibold p-4">
                                    @lang('To Date')
                                </x-table-header-cell>
                                <x-table-header-cell class="text-gray-700 dark:text-gray-300 font-semibold p-4">
                                    @lang('No. of Nights')
                                </x-table-header-cell>
                                <x-table-header-cell class="text-gray-700 dark:text-gray-300 font-semibold p-4">
                                    @lang('Price/Night')
                                </x-table-header-cell>
                                <x-table-header-cell class="text-gray-700 dark:text-gray-300 font-semibold p-4">
                                    @lang('Discount')
                                </x-table-header-cell>
                                <x-table-header-cell class="text-gray-700 dark:text-gray-300 font-semibold p-4">
                                    @lang('Total Amount')
                                </x-table-header-cell>
                            </x-table-row>
                        </x-table-header>

                        <!-- Table Body -->
                        <tbody>
                            <x-table-row
                                class="border-t hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-150 ease-in-out">
                                <x-table-cell class="p-4 text-gray-800 dark:text-gray-200">
                                    {{ $selectedCheckIn->room_number }}
                                </x-table-cell>
                                <x-table-cell class="p-4 text-gray-800 dark:text-gray-200">
                                    {{ $selectedCheckIn->check_in_time }}
                                </x-table-cell>
                                <x-table-cell class="p-4 text-gray-800 dark:text-gray-200">
                                    {{ $selectedCheckIn->check_out_time }}
                                </x-table-cell>
                                <x-table-cell class="p-4 text-gray-800 dark:text-gray-200 number">
                                    {{ $selectedCheckIn->number_of_nights }}
                                </x-table-cell>
                                <x-table-cell class="p-4 text-gray-800 dark:text-gray-200 number">
                                    ₦ {{ number_format($selectedCheckIn->price_per_night ?? 0, 2) }}
                                </x-table-cell>
                                <x-table-cell class="p-4 text-gray-800 dark:text-gray-200 number">
                                    ₦ {{ number_format($selectedCheckIn->coupon_discount ?? 0, 2) }}
                                </x-table-cell>
                                <x-table-cell class="p-4 text-gray-800 dark:text-gray-200 number">
                                    ₦ {{ number_format($selectedCheckIn->total_amount ?? 0, 2) }}
                                </x-table-cell>
                            </x-table-row>
                        </tbody>
                    </x-table>
                </div>
            </div>

            <!-- Main Container -->
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{
                                        pricePerNight: {{ $selectedCheckIn->price_per_night ?? 0 }},
                                        discountPercentage: 0,
                                        discountAmount: 0,
                                        totalAmount: {{ $selectedCheckIn->total_amount ?? 0 }},
                                        advanceAmount: {{ $selectedCheckIn->paid_amount ?? 0 }},
                                        additionalCharges: 0,
                                        restaurantCharge: {{ $restaurantCharge ?? 0 }},  // Dynamic restaurant charge from the database
                                        dueAmount: 0,
                                        payableAmount: 0,
                                        payingAmount: 0,
                                        remainingAmount: 0,
                                        changeAmount: 0,

                                        // Initialize and calculate
                                        init() {
                                            this.calculatePayableAmount();
                                        },

                                        // Function to calculate the discount
                                        calculateDiscount() {
                                            this.discountAmount = (this.totalAmount * (this.discountPercentage / 100)).toFixed(2);
                                            this.calculatePayableAmount();
                                        },

                                        // Function to calculate the payable amount
                                        calculatePayableAmount() {
                                            const basePayable = (this.totalAmount - this.discountAmount + parseFloat(this.additionalCharges) + this.restaurantCharge).toFixed(2);
                                            this.payableAmount = basePayable;

                                            this.dueAmount = (this.payableAmount - this.advanceAmount).toFixed(2);
                                            if (this.dueAmount < 0) {
                                                this.dueAmount = 0;
                                            }
                                        },

                                        // Function to calculate payment
                                        calculatePayment() {
                                            this.remainingAmount = (this.dueAmount - this.payingAmount).toFixed(2);
                                            if (this.payingAmount > this.dueAmount) {
                                                this.changeAmount = (this.payingAmount - this.dueAmount).toFixed(2);
                                                this.remainingAmount = 0;
                                            } else {
                                                this.changeAmount = 0;
                                            }
                                        }
                                    }" x-init="init">

                <!-- Billing Details -->
                <x-filament::section class="mb-4" icon="heroicon-m-home-modern"  icon-color="info">
                    <x-slot name="heading">
                        Billing Details
                    </x-slot>

                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="font-medium">Price Per Night:</span>
                            <span> ₦ {{ number_format($selectedCheckIn->price_per_night ?? 0, 2) }} </span>
                        </div>

                        <!-- Discount Input -->
                        <div class="flex justify-between">
                            <span class="font-medium">Discount (Max-100%):</span>
                            <div class="space-y-1">
                                <input type="number" class="border rounded p-1 w-16 dark:bg-gray-800"
                                    x-model="discountPercentage" x-on:input="calculateDiscount()" placeholder="%" />
                            </div>
                        </div>

                        <!-- Discount Amount Display -->
                        <div class="flex justify-between">
                            <span class="font-medium">Discount Amount:</span>
                            <span> ₦ <span x-text="discountAmount"></span> </span>
                        </div>

                        <!-- Total Amount Display -->
                        <div class="flex justify-between">
                            <span class="font-medium">Total Amount:</span>
                            <span> ₦ {{ number_format($selectedCheckIn->total_amount ?? 0, 2) }}</span>
                        </div>

                        <!-- Advance Amount Display -->
                        <div class="flex justify-between">
                            <span class="font-medium">Advance Amount:</span>
                            <span> ₦ {{ number_format($selectedCheckIn->paid_amount ?? 0, 2) }} </span>
                        </div>

                        <!-- Payable Amount Display -->
                        <div class="flex justify-between">
                            <span class="font-medium">Payable Amount:</span>
                            <span> ₦ <span x-text="payableAmount"></span> </span>
                        </div>

                        <!-- Due Amount Display -->
                        <div class="flex justify-between">
                            <span class="font-medium">Due Amount:</span>
                            <span> ₦ <span x-text="dueAmount"></span> </span>
                        </div>
                    </div>
                </x-filament::section>

                <!-- Additional Charges and Payment Details -->
                <x-filament::section class="mb-4" icon="heroicon-m-currency-dollar"  icon-color="info">
                    <x-slot name="heading">
                        Additional Charges
                    </x-slot>

                    <div class="space-y-2">
                        <!-- Additional Charges Input -->
                        <div class="flex flex-col">
                            <label class="font-medium">Additional Charges:</label>
                            <input type="number" class="border rounded p-2 dark:bg-gray-800" x-model="additionalCharges"
                                x-on:input="calculatePayableAmount()" placeholder="Enter charges" />
                        </div>

                        <!-- Additional Charge Comments -->
                        <div class="flex flex-col mt-4">
                            <label class="font-medium">Comments:</label>
                            <textarea class="border rounded p-2 dark:bg-gray-800"
                                placeholder="Additional Charge Comments"></textarea>
                        </div>
                    </div>

                    <!-- Payments Details -->
                    <h2 class="text-lg font-semibold mt-6 mb-4">Payments Details</h2>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="font-medium">Net Payable Amount:</span>
                            <span>₦ <span x-text="payableAmount"></span></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Advance Amount:</span>
                            <span>₦ {{ number_format($selectedCheckIn->paid_amount ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Due Amount (Updated):</span>
                            <span>₦ <span x-text="dueAmount"></span></span>
                        </div>
                    </div>
                </x-filament::section>

                <!-- Room Posted Bill -->
                <x-filament::section class="mb-4" icon="heroicon-m-receipt-percent"  icon-color="info">
                    <x-slot name="heading">
                        Room Posted Bill
                    </x-slot>

                    <table class="w-full text-left table-auto">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-800">
                                <th class="p-4">Bill Type</th>
                                <th class="p-4">Total (₦)</th>
                                <th class="p-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t ">
                                <td class="p-4">Restaurant</td>
                                <td class="p-4">₦ <span x-text="restaurantCharge"></span></td>
                                <td class="p-4">
                                    <x-filament::button
                                        class="bg-green-500 text-white px-4 py-2 rounded">Print</x-filament::button>
                                </td>
                            </tr>
                            <tr class="border-t">
                                <td class="p-4">Laundry</td>
                                <td class="p-4">₦ 0</td>
                                <td class="p-4">
                                    <x-filament::button
                                        class="bg-green-500 text-white px-4 py-2 rounded">Print</x-filament::button>
                                </td>
                            </tr>
                            <tr class="border-t">
                                <td class="p-4">Car Hire</td>
                                <td class="p-4">₦ 0</td>
                                <td class="p-4">
                                    <x-filament::button
                                        class="bg-green-500 text-white px-4 py-2 rounded">Print</x-filament::button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-200 border-t font-bold dark:bg-gray-800">
                                <td class="p-4">Total</td>
                                <td class="p-4">₦ <span x-text="restaurantCharge"></span></td>
                                <td class="p-4"></td>
                            </tr>
                        </tfoot>
                    </table>
                </x-filament::section>


                <!-- Credit Section Here -->
                <x-filament::section class="mb-4" icon="heroicon-m-credit-card"  icon-color="info">
                    <x-slot name="heading">
                        Credit
                    </x-slot>
                    <!-- Payment Method Select -->
                    <div class="mb-4">
                        <label for="payment-method" class="block text-sm font-medium mb-2">Payment Method</label>
                        <select id="payment-method" class="block w-full p-2 border rounded dark:bg-gray-800">
                            <option value="cash">Cash</option>
                            <option value="credit-card">Credit Card</option>
                            <option value="debit-card">Debit Card</option>
                            <option value="bank-transfer">Bank Transfer</option>
                        </select>
                    </div>

                    <!-- Amount Input -->
                    <div class="mb-4">
                        <label for="amount" class="block text-sm font-medium mb-2">Amount</label>
                        <input id="amount" type="number" class="block w-full p-2 border rounded dark:bg-gray-800"
                            x-model="payingAmount" x-on:input="calculatePayment()" placeholder="Enter Amount">
                    </div>

                    <!-- Optional Remarks -->
                    <div class="mb-4">
                        <label for="remarks" class="block text-sm font-medium mb-2">Remarks (Optional)</label>
                        <textarea id="remarks" class="block w-full p-2 border rounded dark:bg-gray-800" rows="3"
                            placeholder="Add any remarks..."></textarea>
                    </div>

                    <!-- Balance Details Section -->
                    <x-filament::card class="bg-gray-100 rounded-lg p-4 mt-6">
                        <x-slot name="heading">
                            Balance Details
                        </x-slot>

                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>Remaining Amount:</span>
                                <span class="font-semibold">₦ <span x-text="remainingAmount"></span></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Paying Amount:</span>
                                <span class="font-semibold">₦ <span x-text="payingAmount"></span></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Change Amount:</span>
                                <span class="font-semibold">₦ <span x-text="changeAmount"></span></span>
                            </div>
                        </div>
                    </x-filament::card>
                </x-filament::section>

            </div>
            <div class="justify-end mt-4 space-x-8">
                <x-filament::button
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full shadow-md hover:shadow-lg transition-shadow"
                    onclick="window.location.href='{{ route('invoice.generate', ['id' => $selectedCheckIn->id]) }}'">
                    Print

                </x-filament::button>


                <x-filament::button
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full shadow-md hover:shadow-lg transition-shadow"
                    wire:click="checkOut">
                    Check Out
                </x-filament::button>
            </div>


        </div>
    @endif
</x-filament-panels::page>