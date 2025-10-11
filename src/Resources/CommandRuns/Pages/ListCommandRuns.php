<?php

namespace BinaryBuilds\CommandRunner\Resources\CommandRuns\Pages;

use BinaryBuilds\CommandRunner\Resources\CommandRuns\CommandRunResource;
use Filament\Resources\Pages\ListRecords;

class ListCommandRuns extends ListRecords
{
    protected static string $resource = CommandRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
