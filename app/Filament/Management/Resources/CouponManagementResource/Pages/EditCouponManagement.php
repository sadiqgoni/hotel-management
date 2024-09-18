<?php

namespace App\Filament\Management\Resources\CouponManagementResource\Pages;

use App\Filament\Management\Resources\CouponManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCouponManagement extends EditRecord
{
    protected static string $resource = CouponManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
