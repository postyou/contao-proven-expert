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

use Symfony\Component\Cache\Adapter\FilesystemTagAwareAdapter;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
    ;

    $services->load('Postyou\\ContaoProvenExpert\\', '../src/')
        ->exclude('../src/{ContaoManager,DependencyInjection}')
    ;

    $services->set('contao_proven_expert.cache')
        ->class(FilesystemTagAwareAdapter::class)
        ->args(['contao_proven_expert', 0, '%kernel.cache_dir%'])
    ;
};
