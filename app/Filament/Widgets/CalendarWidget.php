<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ReservationResource;
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
                ->title(strip_tags($reservation->guest->name . ' - Room ' . $reservation->room->room_number))
                ->start(Carbon::parse($reservation->check_in_date))
                ->end(Carbon::parse($reservation->check_out_date)->addDay())
                ->allDay(false)
                ->url(ReservationResource::getUrl('view', ['record' => $reservation]), false)
                ->textColor('black')
                ->borderColor('green')
                ->backgroundColor($this->getReservationColor($reservation->status))   
                ->extendedProps([
                    'guest_name' => $reservation->guest->name,
                    'room_type' => $reservation->room->room_type,
                    'reservation_status' => $reservation->status,
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


    /**
     * Set calendar configurations.
     */
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
    
            // Injecting legend directly into the DOM 
            'eventDidMount' => <<<JS
            function() {
                var legend = document.createElement('div');
                legend.style.marginBottom = '15px';   
                legend.innerHTML = `
                    <div style="display: flex; gap: 20px; padding: 10px 0;">
                        <div style="display: flex; align-items: center;">
                            <div style="background-color: #007BFF; width: 20px; height: 20px; margin-right: 8px;"></div><span>Confirmed</span>
                        </div>
                        <div style="display: flex; align-items: center;">
                            <div style="background-color: #FFC107; width: 20px; height: 20px; margin-right: 8px;"></div><span>On Hold</span>
                        </div>
                        <div style="display: flex; align-items: center;">
                            <div style="background-color: #28A745; width: 20px; height: 20px; margin-right: 8px;"></div><span>Checked In</span>
                        </div>
                        <div style="display: flex; align-items: center;">
                            <div style="background-color: #DC3545; width: 20px; height: 20px; margin-right: 8px;"></div><span>Checked Out</span>
                        </div>
                    </div>`;
                var calendarEl = document.getElementById('calendar-container');
                calendarEl.insertBefore(legend, calendarEl.firstChild);
            }
            JS,
            'views' => [
                'dayGridMonth' => [
                    'dayMaxEvents' => false,          
                ],
                'timeGridWeek' => [],               
                'timeGridDay' => [],              
            ],
    
            'eventClick' => <<<JS
            function(info) {
                window.open(info.event.url, '_blank'); 
                info.jsEvent.preventDefault();         
            }
            JS,
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
