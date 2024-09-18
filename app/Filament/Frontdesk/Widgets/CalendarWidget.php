<?php

namespace App\Filament\Frontdesk\Widgets;

use App\Filament\Frontdesk\Resources\ReservationResource;
use Filament\Widgets\Widget;
use App\Models\Reservation;
use Carbon\Carbon;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    protected static ?int $sort = 1;
    protected static bool $isLazy = false;
    /**
     * Fetch events for the calendar based on the start and end dates.
     */
    public function fetchEvents(array $fetchInfo): array
    {
        $start = Carbon::parse($fetchInfo['start'])->startOfDay();
        $end = Carbon::parse($fetchInfo['end'])->endOfDay();
        $reservations = Reservation::query()
            ->where('check_in_date', '>=', $start)
            ->where('check_out_date', '<=', $end)
            ->get();
        return $reservations->map(function (Reservation $reservation) {
            return EventData::make()
                ->id($reservation->id)
                ->title(strip_tags($reservation->guest->name . ' - Room ' . ($reservation->room ? $reservation->room->room_number : 'N/A')))
                ->start(Carbon::parse($reservation->check_in_date))
                ->end(Carbon::parse($reservation->check_out_date)->addDay())
                ->allDay(false)
                // ->url(ReservationResource::getUrl('view', ['record' => $reservation]), false)
                ->textColor('black')
                ->borderColor('green')
                ->backgroundColor($this->getReservationColor($reservation->status))
                ->extendedProps([
                    'guest_name' => $reservation->guest->name,
                    'room_type' => $reservation->room ? $reservation->room->room_type : 'N/A',
                    'reservation_status' => $reservation->status,
                    'class' => 'cursor-pointer transition duration-200 ease-in-out transform hover:scale-105 hover:shadow-lg',
                ])
                ->toArray();
        })->all();
    }
    protected function getReservationColor(string $reservationStatus): string
    {
        $colors = [
            'Confirmed' => '#007BFF',
            'On Hold' => '#FFC107',
            'Checked In' => '#28A745',
            'Checked Out' => '#DC3545',
        ];
        return $colors[$reservationStatus] ?? '#CCCCCC';
    }
    public function config(): array
    {
        return [
            // Header toolbar configuration
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
            'height' => 800,
            'initialView' => 'dayGridMonth',
            'navLinks' => true,
            'weekNumbers' => true,
            'eventDisplay' => 'block',
            'displayEventTime' => false,



        ];
    }
    public function eventDidMount(): string
    {
        return <<<JS
            function({ event, el }) {
                el.setAttribute("x-tooltip", "tooltip");
                el.setAttribute("x-data", "{ tooltip: '" + event.title + "' }");
            }
        JS;
    }

}
