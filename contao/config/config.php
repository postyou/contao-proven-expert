<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-proven-expert.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

use Postyou\ContaoProvenExpert\Cache\ProvenExpertCache;

$GLOBALS['TL_PURGE']['custom']['contao_proven_expert'] = [
    'callback' => [ProvenExpertCache::class, 'invalidateTags'],
];
