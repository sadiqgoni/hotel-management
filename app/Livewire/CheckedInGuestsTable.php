<?php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\CheckInCheckOut;

class CheckedInGuestsTable extends Component
{
    public $isVisible = false;

    protected $listeners = ['showCheckedInGuestsTable'];

    public function showCheckedInGuestsTable()
    {
        $this->isVisible = true;
    }

    public function render()
    {
        return view('livewire.checked-in-guests-table', [
            'checkIns' => $this->isVisible ? CheckInCheckOut::latest()->get() : [], // Fetch only if visible
        ]);
    }
}
