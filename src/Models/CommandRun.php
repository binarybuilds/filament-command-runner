<?php

namespace BinaryBuilds\CommandRunner\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $process_id
 * @property string $started_at
 * @property string|null $completed_at
 * @property string|null $killed_at
 * @property string $command
 * @property int $ran_by
 * @property string|null $output
 * @property int $exit_code
 */
class CommandRun extends Model
{
    protected $fillable = [
        'command', 'ran_by', 'started_at',
    ];

    public function getTable()
    {
        return config('command-runner.table_name');
    }
}
