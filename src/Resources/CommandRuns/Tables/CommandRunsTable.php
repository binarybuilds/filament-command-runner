<?php

namespace BinaryBuilds\CommandRunner\Resources\CommandRuns\Tables;

use BinaryBuilds\CommandRunner\Actions\KillCommandAction;
use BinaryBuilds\CommandRunner\CommandRunnerPlugin;
use BinaryBuilds\CommandRunner\Models\CommandRun;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CommandRunsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns(array_filter([
                TextColumn::make('id')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('command')->wrap()->searchable(),

                TextColumn::make('ran_by')
                    ->formatStateUsing(function ($state) {

                        if (! $state) {
                            return null;
                        }

                        $model = config('auth.providers.users.model');

                        $user = $model::find($state);

                        return $user ? $user->name : null;
                    })->placeholder('-')->label('Ran By'),

                TextColumn::make('started_at')->label('Ran At'),

                TextColumn::make('duration')
                    ->state(function (CommandRun $command) {

                        if (is_null($command->completed_at)) {
                            return '';
                        }

                        $start = Carbon::parse($command->started_at);

                        return $start->diffAsCarbonInterval(Carbon::parse($command->completed_at));
                    }),

                TextColumn::make('status')
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
                        default => 'danger',
                    }),

            ]))
            ->recordActions([
                KillCommandAction::make()->iconButton()->tooltip(__('Kill Command'))->visible(function (CommandRun $command) {
                    return is_null($command->completed_at);
                }),
                ViewAction::make()->iconButton(),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(CommandRunnerPlugin::get()->getCanDeleteHistory()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(CommandRunnerPlugin::get()->getCanDeleteHistory()),
                ]),
                CreateAction::make()->label(__('Run Command')),
            ]);
    }
}
