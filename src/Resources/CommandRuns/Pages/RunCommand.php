<?php

namespace BinaryBuilds\CommandRunner\Resources\CommandRuns\Pages;

use BinaryBuilds\CommandRunner\Models\CommandRun;
use BinaryBuilds\CommandRunner\Resources\CommandRuns\CommandRunResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

use function Illuminate\Support\php_binary;

class RunCommand extends CreateRecord
{
    protected static bool $canCreateAnother = false;

    public function getTitle(): string | Htmlable
    {
        return __('Run Command');
    }

    public function getBreadcrumb(): string
    {
        return __('Run Command');
    }

    protected static string $resource = CommandRunResource::class;

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label(__('Run Command'))
            ->icon(Heroicon::CommandLine);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['ran_by'] = auth()->id();
        $data['started_at'] = now()->toDateTimeString();

        return $data;
    }

    protected function afterCreate(): void
    {
        self::runCommand($this->record);
    }

    public static function runCommand(CommandRun $command): void
    {
        $process = Process::fromShellCommandline(
            self::buildCommand($command),
            base_path(),
            null,
            null,
            null
        );

        $command->started_at = now()->toDateTimeString();
        $process->run();

        $pid = trim($process->getOutput());
        $command->process_id = is_numeric($pid) ? (int) $pid : null;
        $command->save();
    }

    public static function buildCommand(CommandRun $command): string
    {
        $finished = '"' . php_binary() . '" artisan command-runner:capture-status ' . $command->id;

        $logFile = storage_path("command-runs/{$command->id}.log");

        $dir = storage_path('command-runs');

        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $commandString = Str::replace('php', '"' . php_binary() . '"', $command->command);

        if (windows_os()) {
            return 'powershell -NoProfile -Command "$p = Start-Process cmd -ArgumentList '
                . escapeshellarg('/c '
                    . $commandString
                    . ' & set EXITCODE=%ERRORLEVEL% & '
                    . $finished . ' %EXITCODE%')
                . ' -RedirectStandardOutput ' . escapeshellarg($logFile)
                . ' -RedirectStandardError ' . escapeshellarg($logFile)
                . ' -WindowStyle Hidden -PassThru; '
                . 'Write-Output $p.Id"';
        }

        return 'nohup sh -c '
            . escapeshellarg($commandString . ' >> ' . $logFile . ' 2>&1; ' . $finished . ' "$?"')
            . ' >/dev/null 2>&1 & echo $!';
    }
}
