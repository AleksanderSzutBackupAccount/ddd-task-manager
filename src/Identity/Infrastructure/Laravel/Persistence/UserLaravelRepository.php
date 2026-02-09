<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Laravel\Persistence;

use Illuminate\Support\Collection;
use Src\Identity\Domain\User;
use Src\Identity\Domain\UserCollection;
use Src\Identity\Domain\UserRepository;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Identity\Infrastructure\Laravel\Models\UserModel;

final class UserLaravelRepository implements UserRepository
{
    public function upsert(User $user): void
    {
        UserModel::query()->updateOrCreate(
            ['id' => $user->id],
            [
                'name' => $user->name,
                'email' => $user->email,
                'external_id' => $user->externalId,
            ],
        );
    }

    public function find(UserId $id): ?User
    {
        /** @var UserModel|null $model */
        $model = UserModel::query()->where('id', $id)->first();

        return $model?->toEntity();
    }

    public function all(): UserCollection
    {
        /** @var Collection<int, UserModel> $models */
        $models = UserModel::query()
            ->orderBy('id')
            ->get();

        $users = [];

        foreach ($models as $model) {
            $users[] = $model->toEntity();
        }

        return UserCollection::new(...$users);
    }
}
