<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-proven-expert.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoProvenExpert\Cache;

use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ProvenExpertCache
{
    public const NAMESPACE = 'contao_proven_expert';

    public function __construct(
        #[Autowire(service: 'contao_proven_expert.cache')]
        private readonly TagAwareAdapterInterface $cache,
    ) {}

    public static function moduleTag(int|string $id): string
    {
        return self::NAMESPACE.'mod_'.$id;
    }

    public function invalidateTagsForModule(int|string $id): void
    {
        $this->cache->invalidateTags([self::moduleTag($id)]);
    }

    public function invalidateTags(): void
    {
        $this->cache->invalidateTags([self::NAMESPACE]);
    }
}
