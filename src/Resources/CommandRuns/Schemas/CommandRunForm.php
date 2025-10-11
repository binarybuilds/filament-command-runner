<?php

namespace BinaryBuilds\CommandRunner\Resources\CommandRuns\Schemas;

use BinaryBuilds\CommandRunner\CommandRunnerPlugin;
use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CommandRunForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(__('command'))
                    ->required()
                    ->rules(['required', fn (): Closure => CommandRunnerPlugin::get()->getValidationRule()])
                    ->maxLength(450)
                    ->columnSpanFull(),
            ]);
    }
}
