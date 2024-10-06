<?php

namespace App\Filament\Frontdesk\Resources\CheckInResource\Pages;

use App\Filament\Frontdesk\Resources\CheckInResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCheckIns extends ListRecords
{
    protected static string $resource = CheckInResource::class;
    protected static ?string $navigationLabel = 'Check In';
    protected static ?string $modelLabel =  'Check In';
    protected static ?string $title = 'Check In List';

}
