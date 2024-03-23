<?php

namespace App\Filament\Resources\LeaveEntitlementTypeResource\Pages;

use App\Filament\Resources\LeaveEntitlementTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeaveEntitlementType extends EditRecord
{
    protected static string $resource = LeaveEntitlementTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
