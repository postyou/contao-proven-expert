<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-proven-expert
 *
 * (c) POSTYOU Digital- & Filmagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoProvenExpert\Cache;

use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

class ProvenExpertCacheTags
{
    public const NAMESPACE = 'contao_proven_expert';

    private $peCache;

    public function __construct(TagAwareAdapterInterface $peCache)
    {
        $this->peCache = $peCache;
    }

    public static function moduleTag($id): string
    {
        return self::NAMESPACE.'mod_'.$id;
    }

    public function invalidateTagsForModule($id): void
    {
        $this->peCache->invalidateTags([self::moduleTag($id)]);
    }

    public function invalidateTags(): void
    {
        $this->peCache->invalidateTags([self::NAMESPACE]);
    }
}
