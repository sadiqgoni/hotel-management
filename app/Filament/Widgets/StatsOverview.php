<?php

namespace App\Filament\Widgets;

use App\Models\Reservation;
use App\Models\Guest;
use App\Models\Room;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        return [
            $this->getCheckedInGuests(),  // Total number of guests currently checked in
            $this->getActiveReservations(), // Total number of active reservations (confirmed + checked-in)
            $this->getAvailableRooms(),     // Total number of available rooms
            $this->getTotalRoomsBooked(),   // Total number of rooms booked (checked in + confirmed)
            $this->getOccupancyRate(),
            $this->getCancellationRate(),

            // $this->getCheckedInGuests(),
            // $this->getTotalReservations(),
            // $this->getAvailableRooms(),
            // $this->getTotalRevenue(),
            // $this->getPendingPayments(),
            // $this->getRoomOccupancyRate(),
        ];
    }

    // 1. Guests Currently Checked In
    protected function getCheckedInGuests(): Card
    {
        $count = Reservation::where('status', 'Checked In')->count();

        return Card::make('Checked-In Guests', $count)
            ->description('Number of guests currently checked in')
            ->descriptionIcon('heroicon-o-user-group')
            ->color('success');
    }



    // 2. Active Reservations (Confirmed and Checked-In)
    protected function getActiveReservations()
    {
        $title = 'Active Reservations';

        // Query to get the total number of active reservations (confirmed + checked in)
        $activeReservationsCount = Reservation::whereIn('status', ['Confirmed', 'Checked In'])
            ->count();

        return Stat::make($title, $activeReservationsCount)
            ->description('Confirmed or Checked-In Reservations')
            ->descriptionIcon('heroicon-o-calendar-days')
            ->color(Color::Indigo);
    }

    // 3. Available Rooms (Rooms not booked)
    protected function getAvailableRooms()
    {
        $title = 'Available Rooms';

        // Query to get the number of available rooms (status = 'available')
        $availableRoomsCount = Room::where('status', '1')
            ->count();

        return Stat::make($title, $availableRoomsCount)
            ->description('Rooms currently available for booking')
            ->descriptionIcon('heroicon-o-home')
            ->color(Color::Fuchsia);
    }

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
