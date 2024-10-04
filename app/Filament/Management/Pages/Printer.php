<?php

namespace App\Filament\Management\Pages;

use App\Traits\HasTranslatableResource;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components;
use Filament\Forms\Components\Actions\Action as ActionsAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;

class Printer extends Page implements HasActions, HasForms
{
    use HasTranslatableResource;
    use InteractsWithFormActions;

    protected static ?string $navigationIcon = 'heroicon-o-printer';
    protected static ?string $navigationGroup = 'Configurations';

    protected static ?string $navigationLabel = 'Printer Management';
    protected static string $view = 'filament.management.pages.printer';

    public ?array $data = [];

    public function mount()
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Components\Textarea::make('header')
                ->rows(5)
                ->label('Header'),
            Components\TextInput::make('name')
                ->required()
                ->label('Printer Name'),
            Components\Select::make('driver')
                ->default('usb')
                ->options([
                    'usb' => 'USB',
                    // Add more options if needed, like Bluetooth
                ])
                ->label('Driver Type'),
            Grid::make(columns: 3)
                ->schema([
                    Components\TextInput::make('printer')
                    ->required()
                    ->helperText('Select the connected printer')
                    ->readOnly()
                    ->label('Printer Device')
                    ->columnSpan(2),
                Components\TextInput::make('printerId')
                    ->required()
                    ->hintActions([
                        Components\Actions\Action::make('select_printer')
                            ->icon('heroicon-o-printer')
                            ->label('Select Printer')
                            ->extraAttributes([
                                'x-on:click' => 'fetchDeviceByDriver',
                            ]),
                    ])
                    ->readOnly()
                    ->label('Printer ID'),
                ]),
                Components\Textarea::make('footer')
                ->rows(5)
                ->label('Footer'),
        ])->statePath('data');
    }

    public function getFormActions(): array
    {
        return [
            Action::make('save')
            ->label('Save')
            ->extraAttributes([
                'x-on:click' => 'save',
            ]),
        Action::make('test')
            ->label('Test')
            ->color('warning')
            ->icon('heroicon-o-printer')
            ->extraAttributes([
                'x-on:click' => 'test',
            ]),
        ];
    }

    public function validateInput()
    {
        $this->validate([
            'data.printer' => 'required',
            'data.name' => 'required',
        ]);
    }
}
