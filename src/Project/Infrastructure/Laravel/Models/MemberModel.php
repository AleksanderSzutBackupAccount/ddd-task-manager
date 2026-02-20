<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Src\Identity\Domain\ValueObjects\UserExternalId;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Shared\Infrastructure\Laravel\CastableModel;

/**
 * @property-read UserId $id,
 * @property string $name,
 * @property string $email,
 * @property UserExternalId $external_id,
 * @property \DateTimeImmutable $created_at,
 * @property \DateTimeImmutable $updated_at,
 */
final class MemberModel extends CastableModel
{
    protected $table = 'users';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    /**
     * @return BelongsToMany<ProjectModel, $this, Pivot, 'pivot'>
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(
            ProjectModel::class,
            'project_user',
            'user_id',
            'project_id'
        );
    }

    protected $fillable = [
        'id',
        'name',
        'email',
        'external_id',
        'created_at',
        'updated_at',
    ];

    public $casts = [
        'id' => UserId::class,
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];
}
