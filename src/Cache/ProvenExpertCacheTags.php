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

class ProvenExpertCacheTags
{
    public const NAMESPACE = 'contao_proven_expert';

    /** @var TagAwareAdapterInterface */
    private $peCache;

    public function __construct(TagAwareAdapterInterface $peCache)
    {
        $this->peCache = $peCache;
    }

    /**
     * @param int|string $id
     */
    public static function moduleTag($id): string
    {
        return self::NAMESPACE.'mod_'.$id;
    }

    /**
     * @param int|string $id
     */
    public function invalidateTagsForModule($id): void
    {
        $this->peCache->invalidateTags([self::moduleTag($id)]);
    }

    public function invalidateTags(): void
    {
        $this->peCache->invalidateTags([self::NAMESPACE]);
    }
}
