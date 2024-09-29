<!-- Modal wrapper -->
<div x-data="{ open: false }" @open-payment-modal.window="open = true" @close-payment-modal.window="open = false">
    <div x-show="open" class="modal">
        <div class="modal-content">
            @livewire('table-order', ['record' => $record])
        </div>
    </div>
</div>
