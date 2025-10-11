<?php

namespace BinaryBuilds\CommandRunner\Actions;

use BinaryBuilds\CommandRunner\Models\CommandRun;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Process;

class KillCommandAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'kill';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('Kill Command'))
            ->icon(Heroicon::XCircle)
            ->color('danger')
            ->modalHeading(__('Kill Command?'))
            ->modalDescription(__('Are you sure you want to kill this command?'))
            ->requiresConfirmation()
            ->successNotificationTitle(__('command killed successfully!'))
            ->action(function (Action $action, CommandRun $command) {
                try {
                    Process::run('kill ' . $command->process_id)->throw();

                    $command->killed_at = now()->toDateTimeString();
                    $command->completed_at = now()->toDateTimeString();
                    $command->exit_code = 1;

                    $logFile = storage_path('command-runs/' . $command->id . '.log');

                    if (file_exists($logFile)) {
                        $command->output = trim(file_get_contents($logFile));
                        unlink($logFile);
                    }

                    $command->save();

                    return true;
                } catch (\Throwable $exception) {
                    $action->failureNotificationTitle($exception->getMessage());
                    $action->failure();

                    return false;
                }
            });
    }
}
