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

use Contao\ModuleModel;
use Contao\PageModel;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

class ProvenExpertCacheItem
{
    private $cacheItem;

    public function __construct(
        private readonly TagAwareAdapterInterface $peCache,
        PageModel $page,
        private readonly ModuleModel $model,
    ) {
        $key = implode('.', ['contao_proven_expert', $page->rootId, $this->model->id]);
        $this->cacheItem = $this->peCache->getItem($key);
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
                ProvenExpertCacheTags::moduleTag($this->model->id),
            ])
        ;

        $this->peCache->save($this->cacheItem);
    }

    /**
     * Get either the cached version or the db fallback.
     */
    public function get(): string
    {
        return $this->cacheItem->get() ?: $this->model->peHtml;
    }
}
