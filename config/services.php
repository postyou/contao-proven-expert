<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-proven-expert.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Postyou\ContaoProvenExpert\Cache\ProvenExpertCache;
use Symfony\Component\Cache\Adapter\FilesystemTagAwareAdapter;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
            ->bind('$cacheLifetime', param('contao_proven_expert.cache_lifetime'))

        ->load('Postyou\\ContaoProvenExpert\\', '../src/')
            ->exclude('../src/{ContaoManager,DependencyInjection}')

        ->set(ProvenExpertCache::class)
            ->public()

        ->set('contao_proven_expert.cache')
            ->class(FilesystemTagAwareAdapter::class)
            ->args([
                '$namespace' => ProvenExpertCache::NAMESPACE,
                '$directory' => param('kernel.cache_dir'),
            ])
    ;
};
