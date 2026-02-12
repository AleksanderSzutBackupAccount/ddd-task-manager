<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Src\Identity\Infrastructure\Laravel\Models\UserModel;
use Src\Project\Domain\Project;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\ProjectSlug;
use Src\Shared\Infrastructure\Laravel\CastableModel;

final class ProjectModel extends CastableModel
{
    protected $table = 'projects';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name', 'slug', 'created_at', 'updated_at',
    ];

    public $casts = [
        'id' => ProjectId::class,
        'slug' => ProjectSlug::class,
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(UserModel::class, 'project_user', 'project_id', 'user_id');
    }

    public function toEntity(): Project
    {
        $userIds = $this->users()->pluck('users.id')->map(fn ($id) => (string) $id)->all();

        return new Project(
            id: $this->id,
            name: (string) $this->name,
            slug: $this->slug,
            userIds: $userIds,
        );
    }
}
