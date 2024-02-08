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

class ProvenExpertCacheItem
{
    private $cacheItem;

    public function __construct(
        private readonly TagAwareAdapterInterface $peCache,
        int $rootPageId,
        private readonly int $modelId,
    ) {
        $key = implode('.', ['contao_proven_expert', $rootPageId, $modelId]);
        $this->cacheItem = $peCache->getItem($key);
    }

    public function isHit(): bool
    {
        return $this->cacheItem->isHit();
    }

    public function set(string $html): void
    {
        $this->cacheItem
            ->set($html)
            ->expiresAfter(\DateInterval::createFromDateString('1 hour'))
            ->tag([
                ProvenExpertCacheTags::NAMESPACE,
                ProvenExpertCacheTags::moduleTag($this->modelId),
            ])
        ;

        $this->peCache->save($this->cacheItem);
    }

    public function get(): ?string
    {
        return $this->cacheItem->get();
    }
}
