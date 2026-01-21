<?php

namespace BinaryBuilds\CommandRunner;

use BackedEnum;
use BinaryBuilds\CommandRunner\Resources\CommandRuns\CommandRunResource;
use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class CommandRunnerPlugin implements Plugin
{
    use EvaluatesClosures;

    private $validationRule = null;

    private $deleteHistory = true;

    private bool | Closure $authorized = true;

    private string | BackedEnum | null $navigationIcon = HeroIcon::OutlinedCommandLine;

    private string | UnitEnum | null $navigationGroup = 'Settings';

    private string $navigationLabel = 'Command Runner';

    private int | Closure $navigationSort = 9999;

    public function getId(): string
    {
        return 'command-runner';
    }

    public function authorize(bool | Closure $callback): static
    {
        $this->authorized = $callback;

        return $this;
    }

    public function isAuthorized(): bool
    {
        return (bool) $this->evaluate($this->authorized);
    }

    public function validateCommand(callable $commandRule): CommandRunnerPlugin
    {
        $this->validationRule = $commandRule;

        return $this;
    }

    public function navigationLabel(string $navigationLabel): static
    {
        $this->navigationLabel = $navigationLabel;

        return $this;
    }

    public function getNavigationLabel(): string
    {
        return $this->navigationLabel;
    }

    public function navigationGroup(string | UnitEnum | null $navigationGroup): static
    {
        $this->navigationGroup = $navigationGroup;

        return $this;
    }

    public function getNavigationGroup(): string
    {
        return $this->navigationGroup;
    }

    public function navigationSort(int | Closure $sort): static
    {
        $this->navigationSort = $sort;

        return $this;
    }

    public function getNavigationSort(): int
    {
        /** @var int */
        return $this->evaluate($this->navigationSort);
    }

    public function navigationIcon(string | BackedEnum | null $navigationIcon): static
    {
        $this->navigationIcon = $navigationIcon;

        return $this;
    }

    public function getNavigationIcon(): string | Heroicon | null
    {
        return $this->navigationIcon;
    }

    public function canDeleteCommandHistory(callable | bool $deleteHistory): CommandRunnerPlugin
    {
        $this->deleteHistory = $deleteHistory;

        return $this;
    }

    public function getCanDeleteHistory(): callable | bool
    {
        return $this->deleteHistory;
    }

    public function getValidationRule(): ?\Closure
    {
        return $this->validationRule ?? function () {};
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            CommandRunResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
