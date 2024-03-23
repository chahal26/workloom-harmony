<?php

namespace App\Filament\Resources\LeaveEntitlementTypeResource\Pages;

use App\Filament\Resources\LeaveEntitlementTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeaveEntitlementTypes extends ListRecords
{
    protected static string $resource = LeaveEntitlementTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
