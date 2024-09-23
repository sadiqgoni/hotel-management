<?php

namespace App\Filament\Management\Resources\MenuCategoryResource\Pages;

use App\Filament\Management\Resources\MenuCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMenuCategory extends EditRecord
{
    protected static string $resource = MenuCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
