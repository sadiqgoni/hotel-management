<?php

namespace App\Filament\Management\Resources\StaffManagementResource\Pages;

use App\Filament\Management\Resources\StaffManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStaffManagement extends CreateRecord
{
    protected static string $resource = StaffManagementResource::class;
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
}
