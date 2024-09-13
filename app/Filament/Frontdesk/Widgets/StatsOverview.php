<?php

namespace App\Filament\Frontdesk\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Reservation;
use App\Models\Guest;
use App\Models\Room;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\Log;

class StatsOverview extends BaseWidget
{
    public $activeCard = null;  // Track the active card

    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        return [
            $this->getCheckedInGuests(),
            $this->getAvailableRooms(),
            $this->getActiveReservations(),
        ];
    }

    protected function getCheckedInGuests(): Card
    {
        $count = Reservation::where('status', 'Checked In')->count();
    
        return Card::make('Checked-In Guests', $count)
            ->description(description: 'Number of guests currently checked in')
            ->descriptionIcon('heroicon-o-user-group')
            ->color($this->activeCard === 'CheckedInGuests' ? 'primary' : 'success')  // Change border color when active
            ->extraAttributes(attributes: [
                'class' => 'cursor-pointer hover:bg-primary-100',
                'x-on:click' => "\$dispatch('CheckedInGuests')"  // Set the active card
            ]);
    }

    protected function getAvailableRooms(): Card
    {
        $availableRoomsCount = Reservation::where('status', 'Available')->count();
    
        return Card::make('Available Rooms', $availableRoomsCount)
            ->description('Total available rooms')
            ->descriptionIcon('heroicon-o-building-office-2')
            // ->color($this->activeCard === 'AvailableRooms' ? 'primary' : 'success')  // Change border color when active
            ->extraAttributes([
                'class' => 'cursor-pointer hover:bg-primary-100',
                'wire:click' => "\$dispatch('activeCard', 'AvailableRooms')"  // Set the active card
            ]);
    }

    protected function getActiveReservations(): Card
    {
        $reservationsCount = Reservation::where('status', 'Confirmed')->count();
    
        return Card::make('Active Reservations', $reservationsCount)
            ->description('Number of active reservations')
            ->descriptionIcon('heroicon-o-calendar')
            // ->color($this->activeCard === 'ActiveReservations' ? 'primary' : 'success')  // Change border color when active
            ->extraAttributes([
                'class' => 'cursor-pointer hover:bg-primary-100',
                'wire:click' => "\$dispatch('activeCard', 'ActiveReservations')"  // Set the active card
            ]);
    }

    // protected int|string|array $columnSpan = [
    //     'sm' => 1,
    //     'md' => 6,
    //     'lg' => 6
    // ];

    // protected function getColumns(): int
    // {
    //     return 4;
    // }


    // public function redirectToBookings()
    // {
    //     return redirect()->to('/cp/workshop-bookings');
    // }

    // // 2. Active Reservations (Confirmed and Checked-In)
    // protected function getActiveReservations()
    // {
    //     $title = 'Active Reservations';

    //     // Query to get the total number of active reservations (confirmed + checked in)
    //     $activeReservationsCount = Reservation::whereIn('status', ['Confirmed', 'Checked In'])
    //         ->count();

    //     return Stat::make($title, $activeReservationsCount)
    //         ->description('Confirmed or Checked-In Reservations')
    //         ->descriptionIcon('heroicon-o-calendar-days')
    //         ->color(Color::Indigo);
    // }

    // 3. Available Rooms (Rooms not booked)
    // protected function getAvailableRooms()
    // {
    //     $title = 'Available Rooms';

    //     // Query to get the number of available rooms (status = 'available')
    //     $availableRoomsCount = Room::where('status', '1')
    //         ->count();

    //     return Stat::make($title, $availableRoomsCount)
    //         ->description('Rooms currently available for booking')
    //         ->descriptionIcon('heroicon-o-home')
    //         ->color(Color::Fuchsia);
    // }

    // 4. Total Rooms Booked (Checked In or Confirmed)
    protected function getTotalRoomsBooked()
    {
        $title = 'Total Rooms Booked';

        // Query to get the total number of rooms booked (status = 'confirmed' or 'checked_in')
        $totalRoomsBookedCount = Reservation::whereIn('status', ['Confirmed', 'Checked In'])
            ->distinct('room_id')
            ->count('room_id');  // Count distinct rooms

        return Stat::make($title, $totalRoomsBookedCount)
            ->description('Rooms currently booked')
            ->descriptionIcon('heroicon-o-building-office-2')
            ->color('warning');
    }
    protected function getOccupancyRate()
    {
        $totalRooms = Room::count();

        // Prevent division by zero
        if ($totalRooms === 0) {
            return Stat::make('Occupancy Rate', 'N/A')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->description('No rooms available')
                ->color('gray');
        }

        $bookedRooms = Reservation::whereIn('status', ['Confirmed', 'Checked In'])
            ->distinct('room_id')
            ->count();

        $occupancyRate = ($bookedRooms / $totalRooms) * 100;

        return Stat::make('Occupancy Rate', round($occupancyRate, 2) . '%')
            ->descriptionIcon('heroicon-o-chart-bar')
            ->description('Percentage of booked rooms')
            ->color('emerald');
    }

    protected function getCancellationRate()
    {
        $totalReservations = Reservation::count();
        $canceledReservations = Reservation::where('status', 'Canceled')->count();
        $cancellationRate = ($canceledReservations / $totalReservations) * 100;

        return Stat::make('Cancellation Rate', round($cancellationRate, 2) . '%')
            ->description('Percentage of canceled reservations')
            ->color(Color::Red);
    }

}
