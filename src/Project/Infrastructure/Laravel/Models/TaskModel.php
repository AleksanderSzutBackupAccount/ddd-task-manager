<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Identity\Infrastructure\Laravel\Models\UserModel;
use Src\Project\Domain\Task;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\TaskId;
use Src\Project\Domain\ValueObjects\TaskStatus;
use Src\Shared\Infrastructure\Laravel\CastableModel;

final class TaskModel extends CastableModel
{
    protected $table = 'tasks';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'project_id', 'name', 'description', 'status', 'assigned_user_id', 'created_at', 'updated_at',
    ];

    public $casts = [
        'id' => TaskId::class,
        'project_id' => ProjectId::class,
        'status' => TaskStatus::class,
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectModel::class, 'project_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'assigned_user_id');
    }

    public function toEntity(): Task
    {
        $task = Task::create(
            id: $this->id,
            projectId: $this->project_id,
            name: (string) $this->name,
            description: (string) $this->description,
            status: $this->status,
            assignedUserId: $this->assigned_user_id
        );
        // pull events to clear since this is a rehydration shortcut
        $task->pullDomainEvents();
        return $task;
    }
}
