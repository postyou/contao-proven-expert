<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-proven-expert.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoProvenExpert\ApiClient;

class ProvenExpertApiException extends \RuntimeException
{
    /**
     * @param array<string> $errors
     * @param int           $code
     */
    public function __construct(array $errors = [], $code = 0, \Throwable|null $previous = null)
    {
        parent::__construct($this->createMessage($errors), $code, $previous);
    }

    /**
     * @param array<string> $errors
     */
    public function createMessage(array $errors): string
    {
        if ([] === $errors) {
            return 'An unknown error occurred while communicating with the ProvenExpert API.';
        }

        return array_reduce($errors, static fn (string $message, string $error): string => $message .= ' ProvenExpert API-Error: '.$error.'.', '');
    }
}
