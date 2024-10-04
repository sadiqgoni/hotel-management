<div>
    <!-- Form Section -->
    <form wire:submit.prevent="submit">
        {{ $this->form }}
        
        <button type="submit" class="">
            Submit
        </button>
    </form>

    <!-- Modal Section -->
    @if ($showModal)
        <div 
            x-data="{ showModal: @entangle('showModal') }"
            x-show="showModal"
            style="display: none;"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
                <h2 class="text-xl font-bold mb-4">Submitted Guest Information</h2>
                <p><strong>Name:</strong> {{ $data['name'] }}</p>
                <p><strong>Phone Number:</strong> {{ $data['phone_number'] }}</p>
                <p><strong>Preferences:</strong> {{ $data['preferences'] }}</p>
                <p><strong>NIN Number:</strong> {{ $data['nin_number'] }}</p>
                <p><strong>Bonus Code:</strong> {{ $data['bonus_code'] }}</p>
                <p><strong>Stay Count:</strong> {{ $data['stay_count'] }}</p>

                <button 
                    @click="showModal = false" 
                    class="">
                    Close
                </button>
            </div>
        </div>
    @endif
</div>

