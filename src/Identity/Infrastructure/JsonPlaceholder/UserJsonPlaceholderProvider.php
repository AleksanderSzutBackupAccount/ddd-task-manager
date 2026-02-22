<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\JsonPlaceholder;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Src\Identity\Application\External\ExternalUser;
use Src\Identity\Application\Ports\ExternalUserProvider;

final readonly class UserJsonPlaceholderProvider implements ExternalUserProvider
{
    public function __construct(
        private ClientInterface $http,
        private string $baseUrl = 'https://jsonplaceholder.typicode.com',
    ) {
    }

    /**
     * @throws GuzzleException
     */
    public function fetchUsers(): array
    {
        $response = $this->http->request('GET', rtrim($this->baseUrl, '/').'/users', [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'timeout' => 10,
        ]);

        $json = (string) $response->getBody();

        $data = json_decode($json, true);

        if (!is_array($data)) {
            throw new \DomainException('Invalid JSONPlaceholder response.');
        }

        $users = [];

        foreach ($data as $row) {
            if (!is_array($row)) {
                continue;
            }

            $id = isset($row['id']) ? $row['id'] : '';
            $username = isset($row['username']) ? $row['username'] : '';
            $email = isset($row['email']) ? $row['email'] : '';
            $name = isset($row['name']) ? $row['name'] : '';

            if (!is_scalar($id) || !is_scalar($username) || !is_scalar($email) || !is_scalar($name)) {
                continue;
            }

            $idStr = (string) $id;
            $usernameStr = (string) $username;
            $emailStr = (string) $email;
            $nameStr = (string) $name;

            if ('' === $idStr || '' === $usernameStr || '' === $emailStr || '' === $nameStr) {
                continue;
            }

            $users[] = new ExternalUser(
                externalId: $idStr,
                username: $usernameStr,
                email: $emailStr,
                name: $nameStr,
            );
        }

        return $users;
    }
}
