<?php

namespace BinaryBuilds\CommandRunner\Resources\CommandRuns\Schemas;

use BinaryBuilds\CommandRunner\Models\CommandRun;
use Carbon\Carbon;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CommandRunInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextEntry::make('id'),
                TextEntry::make('command')->columnSpan(2),

                TextEntry::make('started_at')
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('completed_at')
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('duration')
                    ->state(function (CommandRun $command) {

                        if (is_null($command->completed_at)) {
                            return '';
                        }

                        $start = Carbon::parse($command->started_at);

                        return $start->diffAsCarbonInterval(Carbon::parse($command->completed_at));
                    }),

                TextEntry::make('status')
                    ->state(function (CommandRun $command) {
                        if (is_null($command->completed_at)) {
                            return 'RUNNING';
                        }

                        if (! is_null($command->killed_at)) {
                            return 'KILLED';
                        }

                        return ((int) $command->exit_code) === 0 ? 'SUCCESS' : 'FAILED';

                    })->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SUCCESS' => 'success',
                        'RUNNING' => 'warning',
                        'KILLED', 'FAILED' => 'danger',
                    }),

                TextEntry::make('exit_code')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'success',
                        default => 'danger',
                    }),

                TextEntry::make('output')
                    ->formatStateUsing(function ($state) {
                        return '<pre style="overflow-x: auto;">' . htmlspecialchars($state) . '</pre>';
                    })->html()->columnSpanFull(),
            ])->columns(3);
    }
}
