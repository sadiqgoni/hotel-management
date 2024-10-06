<?php

namespace App\Filament\Frontdesk\Widgets;

use App\Models\CheckInCheckOut;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Reservation;
use App\Models\Guest;
use App\Models\Room;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;

// class StatsOverview extends BaseWidget
// {
//     public $activeCard = 'active';  // Default active card
//     protected static ?string $pollingInterval = '5s';

//     // Method to update active card
//     public function setActiveCard($card)
//     {
//         $this->activeCard = $card;
//     }

//     // Method to generate common card styles
//     protected function getCardStyles(string $cardType): array
//     {
//         $isActive = $this->activeCard === $cardType;

//         return [
//             'style' => $isActive
//                 ? 'background-color: #ffffff; color: #007bff; border-radius: 12px; border: 2px solid #007bff; box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);'
//                 : 'background-color: #ffffff; color: #333; border-radius: 12px; border: 3px solid #28a745; box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);',
//             'class' => 'cursor-pointer transition duration-200 ease-in-out transform hover:scale-105 hover:shadow-lg',
//             'wire:click' => "\$set('activeCard', '$cardType')",
//         ];
//     }

//     // Method to generate card components
//     protected function createCard(string $title, int $count, string $description, string $icon, string $cardType, string $event): Card
//     {
//         return Card::make($title, $count)
//             ->description($description)
//             ->descriptionIcon($icon)
//             ->color($this->activeCard === $cardType ? '' : 'success')
//             ->extraAttributes(array_merge($this->getCardStyles($cardType), [
//                 'x-on:click' => "\$dispatch('$event')",
//             ]));
//     }

//     // Get statistics array
//     protected function getStats(): array
//     {
//         return [
//             $this->getCheckedInReservations(),
//             $this->getCheckedOutReservations(),
//             $this->getConfirmedReservations(),
//         ];
//     }

//     // Card for Active (Checked-In) Reservations
//     protected function getCheckedInReservations(): Card
//     {
//         $count = CheckInCheckOut::where('status', 'Checked In')->count();
//         return $this->createCard('Checked-In Guests', $count, 'Guests currently checked in', 'heroicon-o-calendar', 'active', 'showCheckedInGuestsTable');
//     }

//     // Card for Checked-Out Reservations
//     protected function getCheckedOutReservations(): Card
//     {
//         $count = CheckInCheckOut::where('status', 'Checked Out')->count();
//         return $this->createCard('Checked-Out Guests', $count, 'Guests who have checked out', 'heroicon-o-check', 'checkedOut', 'showCheckedOutGuestsTable');
//     }

//     // Card for Confirmed Reservations
//     protected function getConfirmedReservations(): Card
//     {
//         $count = Reservation::where('status', 'Confirmed')->count();
//         return $this->createCard('Confirmed Reservations', $count, 'Confirmed Reservations', 'heroicon-o-calendar-days', 'confirmedReservation', 'showConfirmedReservationTable');
//     }
// }


// class StatsOverview extends BaseWidget
// {
//     public $activeCard = null;  // Define reactive property

//     protected static ?string $pollingInterval = '5s';

//     // Method to update active card
//     public function setActiveCard($card)
//     {
//         $this->activeCard = $card;
//     }

//     protected function getStats(): array
//     {
//         return [
//             $this->getActiveReservations(),
//             $this->(),
//             $this->(),
//         ];
//     }

//     protected function getActiveReservations(): Card
//     {
//         $activeReservationsCount = CheckInCheckOut::where('status', 'Checked In')->count();

//         return Card::make('Active Room', $activeReservationsCount)
//             ->description('Currently active room')
//             ->descriptionIcon('heroicon-o-calendar')
//             ->extraAttributes([
//                 'style' => $this->activeCard === 'active' 
//                     ? 'background-color: #f0f0f0; color: #333; border: 2px solid #28a745; border-radius: 8px;' 
//                     : 'background-color: #f0f0f0; color: #333; border: 2px solid #007bff; border-radius: 8px;',
//                 'wire:click' => "\$set('activeCard', 'active')",
//                 'x-on:click' => "\$dispatch('showCheckedInGuestsTable')", 

//             ]);
//     }

//     protected function getInactiveReservations(): Card
//     {
//         $inactiveReservationsCount = Room::where('status', 'Inactive')->count();

//         return Card::make('Inactive Room', $inactiveReservationsCount)
//             ->description('Currently inactive room')
//             ->descriptionIcon('heroicon-o-clock')
//             ->extraAttributes([
//                 'style' => $this->activeCard === 'inactive' 
//                     ? 'background-color: #f0f0f0; color: #333; border: 2px solid #28a745; border-radius: 8px;' 
//                     : 'background-color: #f0f0f0; color: #333; border: 2px solid #007bff; border-radius: 8px;',
//                 'wire:click' => "\$set('activeCard', 'inactive')",  // Change the card on click
//             ]);
//     }
//     protected function getPendingReservations(): Card
// {
//     $pendingReservationsCount = Room::where('status', 'Pending')->count();

//     return Card::make('Pending Room', $pendingReservationsCount)
//         ->description('Pending room requests')
//         ->descriptionIcon('heroicon-o-user-group')
//         ->extraAttributes([
//             'style' => $this->activeCard === 'pending' 
//                 ? 'background-color: #f0f0f0; color: #333; border: 2px solid #28a745; border-radius: 8px;' 
//                 : 'background-color: #f0f0f0; color: #333; border: 2px solid #007bff; border-radius: 8px;',
//             'wire:click' => "\$set('activeCard', 'pending')"  // Updates the active card to "pending" when clicked
//         ]);
// }

//     // protected function getPendingReservations(): Card
//     // {
//     //     $pendingReservationsCount = Room::where('status', 'Pending')->count();

//     //     return Card::make('Pending Room', $pendingReservationsCount)
//     //         ->description('Pending room requests')
//     //         ->descriptionIcon('heroicon-o-user-group')
//     //         ->extraAttributes([
//     //             'x-bind:style' => "activeCard === 'pending' ? 'background-color: #f0f0f0; color: #333; border: 2px solid #28a745; border-radius: 8px;' : 'background-color: #f0f0f0; color: #333; border: 2px solid #007bff; border-radius: 8px;'",
//     //             'x-on:click' => "\$dispatch('setActiveCard', 'pending')",
//     //         ]);
//     // }
//     protected function getCheckedInGuests(): Card
//     {
//         $count = CheckInCheckOut::where('status', 'Checked In')->count();

//         return Card::make('Checked-In Guests', $count)
//             ->description('Number of guests currently checked in')
//             ->descriptionIcon('heroicon-o-user-group')
//             // ->extraAttributes(['style' => 'font-size: 6.75rem']); // Optional: style for smaller text
//             ->extraAttributes([
//                 'class' => 'cursor-pointer hover:bg-primary-100',
//             ]);
//         // ->color('success')
// //             ->extraAttributes([
// //                 // 'x-bind:aria-label' => 'Entradas totais',

//         //                 'class' => 'md:col-span-2 lg:col-span-2 bg-neutral-50', // Add custom CSS classes to the widget container
// // // grid grid-cols-2 gap-4
// //                 // 'class' =>  'cursor-pointer col-span-8 border-0 shadow-lg dark:bg-gray-900 flex justify-en text-indigo-500d',
// //                 // 'x-on:click' => "\$dispatch('showCheckedInGuestsTable')", 
// //             ]);
//     }




//     public function goto($url)
//     {
//         return redirect($url);
//     }


//     // protected int|string|array $columnSpan = [
//     //     'sm' => 1,
//     //     'md' => 6,
//     //     'lg' => 6
//     // ];

//     protected function getColumns(): int
//     {
//         return 4;
//     }


//     // public function redirectToBookings()
//     // {
//     //     return redirect()->to('/cp/workshop-bookings');
//     // }

//     // // 2. Active Reservations (Confirmed and Checked-In)
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

//     // 3. Available Rooms (Rooms not booked)
//     // protected function getAvailableRooms()
//     // {
//     //     $title = 'Available Rooms';

//     //     // Query to get the number of available rooms (status = 'available')
//     //     $availableRoomsCount = Room::where('status', '1')
//     //         ->count();

//     //     return Stat::make($title, $availableRoomsCount)
//     //         ->description('Rooms currently available for booking')
//     //         ->descriptionIcon('heroicon-o-home')
//     //         ->color(Color::Fuchsia);
//     // }

//     // 4. Total Rooms Booked (Checked In or Confirmed)
//     protected function getTotalRoomsBooked()
//     {
//         $title = 'Total Rooms Booked';

//         // Query to get the total number of rooms booked (status = 'confirmed' or 'checked_in')
//         $totalRoomsBookedCount = Reservation::whereIn('status', ['Confirmed', 'Checked In'])
//             ->distinct('room_id')
//             ->count('room_id');  // Count distinct rooms

//         return Stat::make($title, $totalRoomsBookedCount)
//             ->description('Rooms currently booked')
//             ->descriptionIcon('heroicon-o-building-office-2')
//             ->color('warning');
//     }
//     protected function getOccupancyRate()
//     {
//         $totalRooms = Room::count();

//         // Prevent division by zero
//         if ($totalRooms === 0) {
//             return Stat::make('Occupancy Rate', 'N/A')
//                 ->descriptionIcon('heroicon-o-chart-bar')
//                 ->description('No rooms available')
//                 ->color('gray');
//         }

//         $bookedRooms = Reservation::whereIn('status', ['Confirmed', 'Checked In'])
//             ->distinct('room_id')
//             ->count();

//         $occupancyRate = ($bookedRooms / $totalRooms) * 100;

//         return Stat::make('Occupancy Rate', round($occupancyRate, 2) . '%')
//             ->descriptionIcon('heroicon-o-chart-bar')
//             ->description('Percentage of booked rooms')
//             ->color('emerald');
//     }

//     protected function getCancellationRate()
//     {
//         $totalReservations = Reservation::count();
//         $canceledReservations = Reservation::where('status', 'Canceled')->count();
//         $cancellationRate = ($canceledReservations / $totalReservations) * 100;

//         return Stat::make('Cancellation Rate', round($cancellationRate, 2) . '%')
//             ->description('Percentage of canceled reservations')
//             ->color(Color::Red);
//     }

// }
