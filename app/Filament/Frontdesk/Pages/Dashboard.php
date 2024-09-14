<?php

namespace App\Filament\Frontdesk\Pages;

use App\Filament\Frontdesk\Widgets\AvailableRooms;
use App\Filament\Frontdesk\Widgets\LatestChecked;
use App\Filament\Frontdesk\Widgets\LatestCheckedIn;
use App\Filament\Frontdesk\Widgets\LatestCheckedOut;
use App\Filament\Frontdesk\Widgets\StatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;
use Livewire\Component;
class Dashboard extends BaseDashboard
{
    public $showLatestChecked = true; // Show "Checked-In" table by default
    public $showLatestCheckedOut = false; // Hide "Checked-Out" table by default

    protected $listeners = [
        'showCheckedInGuestsTable' => 'showCheckedIn',
        'showCheckedOutGuestsTable' => 'showCheckedOut',
    ];

    // Method to show "Checked-In" table and hide the "Checked-Out" table
    public function showCheckedIn()
    {
        $this->showLatestChecked = true;
        $this->showLatestCheckedOut = false; // Hide the "Checked-Out" table
    }

    // Method to show "Checked-Out" table and hide the "Checked-In" table
    public function showCheckedOut()
    {
        $this->showLatestCheckedOut = true;
        $this->showLatestChecked = false; // Hide the "Checked-In" table
    }

    public function getWidgets(): array
    {
        $widgets = [
            StatsOverview::class,  // Always show the stats overview
        ];

        // Conditionally show the "Latest Checked-In Guests" table
        if ($this->showLatestChecked) {
            $widgets[] = LatestCheckedIn::class;
        }

        // Conditionally show the "Latest Checked-Out Guests" table
        if ($this->showLatestCheckedOut) {
            $widgets[] = LatestCheckedOut::class;
        }

        return $widgets;
    }
}


