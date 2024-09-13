<?php 
namespace App\Filament\Frontdesk\Widgets;

use App\Models\CheckInCheckOut;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Table;

class LatestChecked extends BaseWidget
{
    public $isVisible = false; // Hide the table by default

    protected $listeners = ['showCheckedInGuestsTable' => 'showTable']; // Listen for the event

    public function showTable()
    {
        $this->isVisible = true; // Show the table when the event is triggered
    }

    public function table(Table $table): Table
    {
        // Only render the table if $isVisible is true
        if ($this->isVisible) {
            return $table
                ->query(CheckInCheckOut::query()->latest())
                ->columns([
                    Split::make([
                        Stack::make([
                            TextColumn::make('reservation.guest.name')
                                ->label('Guest Name')
                                ->sortable()
                                ->searchable()
                                ->alignLeft(),
                            TextColumn::make('reservation.guest.email')
                                ->label('Email Address')
                                ->sortable()
                                ->searchable()
                                ->alignLeft(),
                        ])->space(),
                        Stack::make([
                            TextColumn::make('check_in_time')
                                ->label('Check-In Time')
                                ->sortable()
                                ->alignLeft(),
                            TextColumn::make('check_out_time')
                                ->label('Check-Out Time')
                                ->sortable()
                                ->alignLeft(),
                        ])->space(2),
                    ])->from('md'),
                ]);
        }

        // If the table is not visible, return an empty table
        return $table->query(CheckInCheckOut::query()->whereNull('id'));
    }
}
