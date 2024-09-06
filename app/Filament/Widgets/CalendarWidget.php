<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ReservationResource;
use App\Models\Reservation;
use Carbon\Carbon;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Data\EventData;
use Filament\Actions\Action;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    protected static ?int $sort = 1;
    // public Model|string|null $model = Reservation::class;
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
                ->backgroundColor($this->getRoomColor($reservation->room->room_type_id))
                ->extendedProps([
                    'guest_name' => $reservation->guest->name,
                    'room_type' => $reservation->room->room_type_id,
                    'reservation_status' => $reservation->status,
                ])  // Add extra data to the event
                ->toArray();
        })->all();
    }

    /**
     * Assign different colors to rooms based on room type.
     */
    protected function getRoomColor(int $roomTypeId): string
    {
        $colors = [
            1 => '#FF5733',  // Room Type 1: Orange
            2 => '#33FF57',  // Room Type 2: Green
            3 => '#FFD700',  // Room Type 3: Yellow
            4 => '#FF69B4',  // Room Type 4: Pink
            5 => '#f59e0b',  // Room Type 5: Amber
        ];

        return $colors[$roomTypeId] ?? '#FFFFFF';  // Default: White
    }

    /**
     * Set calendar configurations.
     */
    public function config(): array
    {
        return [
            'headerToolbar' => [
                'left' => 'prev,next today listMonth',  // Navigation controls
                'center' => 'title',                    // Calendar title
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',  // View controls
            ],
            'height' => 800,                           // Set calendar height
            'initialView' => 'dayGridMonth',            // Default view when calendar loads
            'navLinks' => true,                         // Allow navigation by clicking on dates
            'weekNumbers' => true,                      // Show week numbers on the calendar
            // 'dayMaxEvents' => true,                     // Limit the number of events shown per day
            'eventDisplay' => 'block',                  // Display events as blocks
            'displayEventTime' => false,                // Disable displaying time on events
            'views' => [                                // Customize different views
                'dayGridMonth' => [
                    'dayMaxEvents' => false,             // Limit events in month view
                    'eventLimit' => 5,                  // Maximum number of events before showing "+more"
                ],


                'listWeek' => [                         // Week view with a list of events
                    'listDayAltFormat' => 'DD/MM/YYYY', // Custom date format
                ],
            ],
            'eventClick' => <<<JS
            function(info) {
                window.open(info.event.url, '_blank');
                info.jsEvent.preventDefault(); // Prevent the browser from following the URL.
            }
        JS,
            'select' => <<<JS
            function(info) {
                alert('Selected from ' + info.startStr + ' to ' + info.endStr);
            }
        JS,
            'eventDidMount' => <<<JS
            function(info) {
                // Add a tooltip with event title
                var tooltip = new Tooltip(info.el, {
                    title: info.event.title,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            }
        JS,
        ];
    }


    /**
     * Define the action to create a new reservation.
     */
    // protected function headerActions(): array
    // {
    //     return [
    //         Action::make('create')
    //             ->url(ReservationResource::getUrl('create'))
    //             ->label('Create Reservation')
    //             ->icon('heroicon-o-plus')
    //             ->color('success')
    //             ->tooltip('Create a new reservation')
    //             ->openUrlInNewTab(),
    //     ];
    // }

    /**
     * Define modal actions, if needed.
     */
    // protected function modalActions(): array
    // {
    //     return [];
    // }

    /**
     * Define the view action for events.
     */
    // protected function viewAction(): Action
    // {
    //     return Action::make('view')
    //         ->url(fn (Reservation $reservation) => ReservationResource::getUrl('view', ['record' => $reservation->id]))
    //         ->openUrlInNewTab();
    // }

    /**
     * Get the form schema for the reservation resource.
     *   */
    //

    // public function getFormSchema(): array
    // {
    //     return ReservationResource::getFormSchema();
    // }

    /**
     * Custom event mount function for tooltip or other purposes.
     */
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
