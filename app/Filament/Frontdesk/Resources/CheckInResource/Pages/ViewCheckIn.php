<?php 
namespace App\Filament\Frontdesk\Resources\CheckInResource\Pages;

use App\Filament\Frontdesk\Resources\CheckInResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCheckIn extends ViewRecord
{
    protected static string $resource = CheckInResource::class;

    protected function getActions(): array
    {
        return [];
    }
}