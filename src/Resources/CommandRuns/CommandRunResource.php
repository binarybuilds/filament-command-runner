<?php

namespace BinaryBuilds\CommandRunner\Resources\CommandRuns;

use BinaryBuilds\CommandRunner\Models\CommandRun;
use BinaryBuilds\CommandRunner\Resources\CommandRuns\Pages\ListCommandRuns;
use BinaryBuilds\CommandRunner\Resources\CommandRuns\Pages\RunCommand;
use BinaryBuilds\CommandRunner\Resources\CommandRuns\Pages\ViewCommandRun;
use BinaryBuilds\CommandRunner\Resources\CommandRuns\Schemas\CommandRunForm;
use BinaryBuilds\CommandRunner\Resources\CommandRuns\Schemas\CommandRunInfolist;
use BinaryBuilds\CommandRunner\Resources\CommandRuns\Tables\CommandRunsTable;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BinaryBuilds\CommandRunner\CommandRunnerPlugin;
use UnitEnum;
use BackedEnum;

class CommandRunResource extends Resource
{
    protected static ?string $slug = 'command-runner';

    protected static function getPlugin(): CommandRunnerPlugin
    {
        return CommandRunnerPlugin::get();
    }

    public static function getNavigationLabel(): string
    {
        return static::getPlugin()->getNavigationLabel();
    }

    public static function canAccess(): bool
    {
        return static::getPlugin()->isAuthorized();
    }

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        return static::getPlugin()->getNavigationGroup();
    }

    public static function getNavigationSort(): ?int
    {
        return static::getPlugin()->getNavigationSort();
    }

    public static function getNavigationIcon(): string | BackedEnum | Heroicon | null
    {
        return static::getPlugin()->getNavigationIcon();
    }

    public static function getBreadcrumb(): string
    {
        return __('Command Runner');
    }

    protected static ?string $model = CommandRun::class;

    public static function infolist(Schema $schema): Schema
    {
        return CommandRunInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommandRunsTable::configure($table)
            ->defaultSort('id', 'desc');
    }

    public static function form(Schema $schema): Schema
    {
        return CommandRunForm::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'create' => RunCommand::route('/create'),
            'index' => ListCommandRuns::route('/'),
            'view' => ViewCommandRun::route('/{record}'),
        ];
    }
}
