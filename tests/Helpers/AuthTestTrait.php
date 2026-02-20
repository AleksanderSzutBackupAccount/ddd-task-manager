<?php

declare(strict_types=1);

namespace Tests\Helpers;

use Src\Identity\Domain\ValueObjects\UserId;
use Src\Identity\Infrastructure\Laravel\Models\UserModel;
use Src\Shared\Application\Auth\TokenGeneratorInterface;

trait AuthTestTrait
{
    public function getUser(?UserId $id): UserModel
    {
        if (! $id) {
            return UserModel::factory()->create();
        }

        return UserModel::query()->findOrFail($id);
    }

    protected function getUserJwtToken(?UserId $id = null): string
    {
        $user = $this->getUser($id);

        return app(TokenGeneratorInterface::class)->generate($user->id->value, ['email' => $user->email->value]);
    }

    protected function callAsAuthorized(?UserId $id = null): self
    {
        $token = $this->getUserJwtToken($id);

        return $this->withToken($token);
    }
}
