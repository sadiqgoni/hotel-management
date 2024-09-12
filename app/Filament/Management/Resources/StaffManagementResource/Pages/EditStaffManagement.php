<?php

namespace App\Filament\Management\Resources\StaffManagementResource\Pages;

use App\Filament\Management\Resources\StaffManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStaffManagement extends EditRecord
{
    protected static string $resource = StaffManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
