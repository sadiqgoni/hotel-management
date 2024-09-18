<?php

namespace App\Filament\Housekeeper\Resources\MaintenanceRequestResource\Pages;

use App\Filament\Housekeeper\Resources\MaintenanceRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMaintenanceRequest extends CreateRecord
{
    protected static string $resource = MaintenanceRequestResource::class;
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['user_id'] = auth()->id();


        return $data;
    }
    
}
