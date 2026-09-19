<?php

namespace App\Filament\Resources\RppResource\Pages;

use App\Filament\Resources\RppResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRpps extends ListRecords
{
    protected static string $resource = RppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
