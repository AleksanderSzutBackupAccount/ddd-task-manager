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
        private string          $baseUrl = 'https://jsonplaceholder.typicode.com',
    ) {}

    /**
     * @throws GuzzleException
     */
    public function fetchUsers(): array
    {
        $response = $this->http->request('GET', rtrim($this->baseUrl, '/') . '/users', [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'timeout' => 10,
        ]);

        $json = (string) $response->getBody();

        $data = json_decode($json, true);

        if (! is_array($data)) {
            throw new \DomainException('Invalid JSONPlaceholder response.');
        }

        $users = [];

        foreach ($data as $row) {
            if (! is_array($row)) {
                continue;
            }

            $id = isset($row['id']) ? (string) $row['id'] : '';
            $username = isset($row['username']) ? (string) $row['username'] : '';
            $email = isset($row['email']) ? (string) $row['email'] : '';
            $name = isset($row['name']) ? (string) $row['name'] : '';

            if ($id === '' || $username === '' || $email === '' || $name === '') {
                continue;
            }

            $users[] = new ExternalUser(
                externalId: $id,
                username: $username,
                email: $email,
                name: $name,
            );
        }

        return $users;
    }
}
