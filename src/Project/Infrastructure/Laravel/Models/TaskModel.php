<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Project\Domain\Task;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\TaskId;
use Src\Project\Domain\ValueObjects\TaskStatus;
use Src\Shared\Infrastructure\Laravel\CastableModel;

/**
 * @property TaskId $id
 * @property ProjectId $project_id
 * @property string $name
 * @property string $description
 * @property TaskStatus $status
 * @property string|null $assigned_user_id
 */
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

    /**
     * @return BelongsTo<ProjectModel, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectModel::class, 'project_id');
    }

    /**
     * @return BelongsTo<MemberModel, $this>
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(MemberModel::class, 'assigned_user_id');
    }

    public function toEntity(): Task
    {
        $task = Task::create(
            id: $this->id,
            projectId: $this->project_id,
            name: $this->name,
            description: $this->description,
            status: $this->status,
            assignedUserId: UserId::fromNullable($this->assigned_user_id)
        );
        $task->pullDomainEvents();

        return $task;
    }
}
