<?php

namespace App\Filament\Resources\LeaveEntitlementResource\Pages;

use App\Filament\Resources\LeaveEntitlementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeaveEntitlement extends EditRecord
{
    protected static string $resource = LeaveEntitlementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
