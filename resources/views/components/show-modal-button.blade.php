<!-- resources/views/components/show-modal-button.blade.php -->
<div>
    <!-- Button to open the modal -->
    <button 
        @click="isOpen = true" 
        class="px-4 py-2 bg-blue-600 text-white rounded">
        Show Modal
    </button>

    <!-- Modal -->
    <div x-show="isOpen" 
         style="display: none;" 
         class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-xl font-bold mb-4">Modal Title</h2>
            <p>Here is some content inside the modal.</p>
            <button 
                @click="isOpen = false" 
                class="mt-4 px-4 py-2 bg-red-500 text-white rounded">
                Close
            </button>
        </div>
    </div>
</div>
