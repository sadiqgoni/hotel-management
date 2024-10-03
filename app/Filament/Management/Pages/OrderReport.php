<?php
namespace App\Filament\Management\Pages;

use App\Services\OrderReportService;
use App\Traits\HasReportPageSidebar;
use App\Traits\HasTranslatableResource;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Livewire\Attributes\Url;


class OrderReport extends Page implements HasActions, HasForms
{
    use HasReportPageSidebar, HasTranslatableResource, InteractsWithFormActions, InteractsWithForms;


    protected static ?string $title = ''; // Updated title
    public static ?string $label = 'Meal Order Report'; // Updated label
    protected static string $view = 'filament.management.pages.order-report';
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
    #[Url]
    public ?array $data = [
        'start_date' => null,
        'end_date' => null,
    ];

    public $reports = null;
    public $header = null;
    public $footer = null;

    public function mount()
    {
        // Initialize empty report data
        $this->data['start_date'] = $this->data['start_date'] ?? now()->startOfMonth()->toDateString();
        $this->data['end_date'] = $this->data['end_date'] ?? now()->endOfMonth()->toDateString();
        $this->reports = [];
        $this->header = [];
        $this->footer = [];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            DatePicker::make(name: 'start_date')
            ->default(now()->startOfMonth())
            ->date()
            ->required()
            ->closeOnDateSelection()
            ->native(false),
            DatePicker::make('end_date')
            ->date()
            ->closeOnDateSelection()
            ->native(false)
            ->required()
            ->default(now()->endOfMonth())
        ])->columns(2)->statePath('data');
    }

    protected function validateReportDates()
    {
        $this->validate([
            'data.start_date' => 'required|date',
            'data.end_date' => 'required|date|after_or_equal:data.start_date',
        ]);
    }

    public function generate(OrderReportService $orderReportService)
    {
        $this->validateReportDates();
        $report = $orderReportService->generate($this->data);
        $this->reports = $report['reports'];
        $this->header = $report['header'];
        $this->footer = $report['footer'];
    }

    public function downloadPdf(OrderReportService $orderReportService)
    {
        $this->validateReportDates();
        return redirect()->route('order-report.generate', [
            'start_date' => $this->data['start_date'],
            'end_date' => $this->data['end_date'],
        ]);
    }

    public function getFormActions(): array
    {
        return [
            Action::make(__('Generate'))->action('generate')->color('primary'),
            Action::make(__('Print'))->color('warning')->extraAttributes(['id' => 'print-btn'])->icon('heroicon-o-printer'),
            Action::make('download-pdf')->label(__('Download as PDF'))->action('downloadPdf')->color('warning')->icon('heroicon-o-arrow-down-on-square'),
        ];
    }
}

// class OrderReport extends Page implements HasActions, HasForms
// {
//     use HasReportPageSidebar, HasTranslatableResource, InteractsWithFormActions, InteractsWithForms;

//     protected static ?string $title = 'Order Report';

//     public static ?string $label = 'Meal Order Report';

//     // protected static ?string $navigationIcon = 'heroicon-o-document-text';

//     protected static string $view = 'filament.management.pages.order-report';
//     public static function shouldRegisterNavigation(): bool
//     {
//         return request()->routeIs('filament.management.pages.restaurant-report');
//     }

//     #[Url]
//     public ?array $data = [
//         'start_date' => null,
//         'end_date' => null,
//     ];

//     public $reports = null;
//     public $header = null;
//     public $footer = null;

//     public function mount()
//     {
//         // Initialize empty report data in case form is not yet submitted
//         $this->reports = [];
//         $this->header = [];
//         $this->footer = [];
//     }

//     // Form definition for start_date and end_date
//     public function form(Form $form): Form
//     {
//         return $form->schema([
//             DatePicker::make('start_date')
//                 ->label('Start Date')
//                 ->required()
//                 ->default(now()->startOfMonth())
//                 ->closeOnDateSelection()
//                 ->native(false),

//             DatePicker::make('end_date')
//                 ->label('End Date')
//                 ->required()
//                 ->default(now()->endOfMonth())
//                 ->closeOnDateSelection()
//                 ->native(false),
//         ])
//             ->columns(2)
//             ->statePath('data');
//     }

//     // Define the form actions like Generate, Print, and Download PDF
//     public function getFormActions(): array
//     {
//         return [
//             Action::make(__('Generate'))
//                 ->action('generate')
//                 ->color('primary'),

//             Action::make(__('Print'))
//                 ->color('warning')
//                 ->extraAttributes([
//                     'id' => 'print-btn',
//                 ])
//                 ->icon('heroicon-o-printer'),

//             Action::make('download-pdf')
//                 ->label(__('Download as PDF'))
//                 ->action('downloadPdf')
//                 ->color('warning')
//                 ->icon('heroicon-o-arrow-down-on-square'),
//         ];
//     }

//     // Generate report using the OrderReportService
//     public function generate(OrderReportService $orderReportService)
//     {
//         // Validate the form before generating the report
//         $this->validate([
//             'data.start_date' => 'required|date',
//             'data.end_date' => 'required|date|after_or_equal:data.start_date',
//         ]);

//         // Generate the report
//         $report = $orderReportService->generate($this->data);

//         // Set the report data to display on the page
//         $this->reports = $report['reports'];
//         $this->header = $report['header'];
//         $this->footer = $report['footer'];
//     }

//     // Handle the PDF download
//     public function downloadPdf(OrderReportService $orderReportService)
//     {
//         // Validate before generating the PDF
//         $this->validate([
//             'data.start_date' => 'required|date',
//             'data.end_date' => 'required|date|after_or_equal:data.start_date',
//         ]);

//         // Generate the report (you can modify this to export the actual PDF file)
//         $report = $orderReportService->generate($this->data);

//         // Logic for PDF download (can be customized to return a real PDF file)
//         // Example: You might use a package like DomPDF or another PDF generation library here
//         return $this->redirectRoute('order-report.generate', $this->data);
//     }
// }
