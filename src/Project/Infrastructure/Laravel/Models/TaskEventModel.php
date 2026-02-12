<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Models;

use Src\Shared\Infrastructure\Laravel\CastableModel;

final class TaskEventModel extends CastableModel
{
    protected $table = 'task_events';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id', 'task_id', 'event_type', 'payload', 'occurred_on',
    ];

    public $casts = [
        'payload' => 'array',
        'occurred_on' => 'immutable_datetime',
    ];
}
