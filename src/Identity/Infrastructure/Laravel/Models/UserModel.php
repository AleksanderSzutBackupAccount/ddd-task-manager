<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Laravel\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Src\Identity\Domain\User;
use Src\Identity\Domain\ValueObjects\UserEmail;
use Src\Identity\Domain\ValueObjects\UserExternalId;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Identity\Domain\ValueObjects\UserName;
use Src\Shared\Infrastructure\Laravel\CastableModel;

/**
 * @property-read UserId $id,
 * @property UserName $name,
 * @property UserEmail $email,
 * @property UserExternalId $external_id,
 * @property \DateTimeImmutable $created_at,
 * @property \DateTimeImmutable $updated_at,
 */
final class UserModel extends CastableModel
{
    /** @use HasFactory<UserFactory>*/
    use HasFactory;

    protected $table = 'users';

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
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
        'name' => UserName::class,
        'email' => UserEmail::class,
        'external_id' => UserExternalId::class,
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];

    public function toEntity(): User
    {
        return new User(
            id: $this->id,
            name: $this->name,
            email: $this->email, externalId: $this->external_id
        );
    }
}
