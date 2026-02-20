<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Collection;
use Src\Project\Domain\Project;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\ProjectSlug;
use Src\Shared\Infrastructure\Laravel\CastableModel;

/**
 * @property ProjectId $id
 * @property ProjectSlug $slug
 * @property string $name
 * @property Collection<int, MemberModel> $users
 */
final class ProjectModel extends CastableModel
{
    protected $table = 'projects';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $with = ['users'];

    protected $fillable = [
        'id', 'name', 'slug', 'created_at', 'updated_at',
    ];

    public $casts = [
        'id' => ProjectId::class,
        'slug' => ProjectSlug::class,
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];

    /**
     * @return BelongsToMany<MemberModel, $this, Pivot, 'pivot'>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(MemberModel::class, 'project_user', 'project_id', 'user_id');
    }

    public function toEntity(): Project
    {
        $userIds = $this->users()->pluck('users.id')->map(fn ($id) => (string) $id)->all();

        return new Project(
            id: $this->id,
            name: $this->name,
            slug: $this->slug,
            userIds: $userIds,
        );
    }
}
