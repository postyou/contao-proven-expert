<?php

/*
 * This file is part of postyou/contao-proven-expert
 *
 * (c) POSTYOU Digital- & Filmagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoProvenExpert\ApiClient;

class ProvenExpertApiException extends \RuntimeException
{
    public function __construct(array $errors = [], $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($this->createMessage($errors), $code, $previous);
    }

    public function createMessage(array $errors): string
    {
        if ([] === $errors) {
            return 'An unknown error occurred while communicating with the ProvenExpert API.';
        }

        return array_reduce($errors, static function (string $message, string $error): string {
            return $message .= ' ProvenExpert API-Error: '.$error.'.';
        }, '');
    }
}
