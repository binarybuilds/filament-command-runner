<?php

namespace BinaryBuilds\CommandRunner;

use BinaryBuilds\CommandRunner\Commands\CaptureCommandStatus;
use BinaryBuilds\CommandRunner\Commands\PurgeCommandHistory;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CommandRunnerServiceProvider extends PackageServiceProvider
{
    public static string $name = 'command-runner';

    public static string $viewNamespace = 'command-runner';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasMigration('2025_10_09_001423_create_command_runs_table')
            ->hasCommands([
                CaptureCommandStatus::class,
                PurgeCommandHistory::class,
            ]);
    }
}
