<?php

namespace BinaryBuilds\CommandRunner\Models;

use Illuminate\Database\Eloquent\Model;

class CommandRun extends Model
{
    protected $fillable = [
        'command', 'ran_by',
    ];

    public function getTable()
    {
        return config('command-runner.table_name');
    }
}
