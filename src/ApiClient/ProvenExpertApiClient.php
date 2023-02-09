<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-proven-expert
 *
 * (c) POSTYOU Digital- & Filmagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoProvenExpert\ApiClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProvenExpertApiClient
{
    private const BASE_URI = 'https://www.provenexpert.com/api/v1/';

    private $client;

    private $credentials;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function setCredentials(array $credentials): void
    {
        $this->credentials = $credentials;
    }

    public function createWidget(array $data): array
    {
        return $this->request('widget/create', $data);
    }

    public function getRichsnippet(array $data = []): array
    {
        return $this->request('rating/summary/richsnippet', $data);
    }

    private function request(string $path, array $data): array
    {
        $response = $this->client->request(
            'POST',
            self::BASE_URI.$path,
            [
                'auth_basic' => $this->credentials,
                'body' => [
                    'data' => $data,
                ],
            ]
        );

        return $response->toArray();
    }
}
