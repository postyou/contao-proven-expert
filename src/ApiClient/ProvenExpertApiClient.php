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

use Contao\Config;
use Contao\CoreBundle\Monolog\ContaoContext;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProvenExpertApiClient
{
    private const BASE_URI = 'https://www.provenexpert.com/api/v1/';

    private $client;

    private $credentials;

    private $logger;

    public function __construct(HttpClientInterface $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
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

        $content = [];

        try {
            $content = $response->toArray(true);

            if (!isset($content['status'])) {
                throw new ProvenExpertApiException(['The ProvenExpert API returned an invalid response']);
            }

            if ('error' === $content['status']) {
                throw new ProvenExpertApiException($content['errors']);
            }
        } catch (ExceptionInterface|ProvenExpertApiException $e) {
            if (Config::get('debugMode')) {
                // Rethrow the exception in debug mode
                throw $e;
            }

            $this->logger->error(
                $e->getMessage().' Try activating the debug mode for more details.',
                ['contao' => new ContaoContext(__METHOD__, ContaoContext::ERROR)]
            );
        }

        if (!isset($content['html'])) {
            $content['html'] = '';
        }

        return $content;
    }
}
