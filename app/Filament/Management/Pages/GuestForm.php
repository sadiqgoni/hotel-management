<?php
namespace App\Filament\Management\Pages;


use Livewire\Component;
use Filament\Forms;
use App\Models\Guest;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Livewire\Attributes\Url;

class GuestForm extends Page implements HasForms

{
    use InteractsWithForms;

    public ?array $data = [];  // Stores the form input data
    public $showModal = false; // Controls the modal visibility

    protected static string $view = 'filament.management.pages.guest-form';

    // Define the form schema
    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('phone_number')
                    ->label('Phone Number')
                    ->tel(),
                TextInput::make('preferences')
                    ->label('Preferences')
                    ->placeholder('Any preferences?'),
                TextInput::make('nin_number')
                    ->label('NIN Number'),
                TextInput::make('bonus_code')
                    ->label('Bonus Code'),
                TextInput::make('stay_count')
                    ->label('Stay Count')
                    ->numeric(),
            ])
            ->statePath('data');
    }

    // Handle form submission
    public function submit()
    {
        $this->validate(); // Validate inputs
        
        // Save the guest data (you could also trigger some custom action)
        Guest::create($this->data);

        // Show the modal with the submitted data
        $this->showModal = true;
    }

}
