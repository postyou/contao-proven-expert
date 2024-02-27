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
use Doctrine\DBAL\Connection;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CacheableContent
{
    private PageModel|null $pageModel = null;

    private ModuleModel|null $model = null;

    private string $dbFallback = '';

    public function __construct(
        #[Autowire(service: 'contao_proven_expert.cache')]
        private readonly TagAwareAdapterInterface $cache,
        private readonly Connection $db,
        private readonly int $cacheLifetime,
    ) {}

    public function setContext(PageModel $pageModel, ModuleModel $model): self
    {
        $this->pageModel = $pageModel;
        $this->model = $model;

        return $this;
    }

    public function setDbFallback(string $dbFallback): self
    {
        $this->dbFallback = $dbFallback;

        return $this;
    }

    /**
     * @param callable():string $callback
     */
    public function getResult(callable $callback): string
    {
        if (!$this->pageModel || !$this->model) {
            throw new \LogicException('You have to call setContext first.');
        }

        $this->pageModel->loadDetails();

        if (empty($this->pageModel->peUploadDirectory)) {
            return '';
        }

        $key = implode('.', [ProvenExpertCache::NAMESPACE, $this->pageModel->rootId, $this->model->id]);
        $cacheItem = $this->cache->getItem($key);

        if (!$cacheItem->isHit()) {
            $html = $callback();

            if (!empty($html)) {
                $this->saveContent($cacheItem, $html, $this->model->id);
            }
        }

        // Get either the cached version or the db fallback.
        return $cacheItem->get() ?: ($this->dbFallback ? $this->model->{$this->dbFallback} : '');
    }

    private function saveContent(CacheItem $cacheItem, string $content, int $moduleId): void
    {
        $cacheItem
            ->set($content)
            ->expiresAfter($this->cacheLifetime)
            ->tag([
                ProvenExpertCache::NAMESPACE,
                ProvenExpertCache::moduleTag($moduleId),
            ])
        ;

        $this->cache->save($cacheItem);

        if ($this->dbFallback) {
            $this->db->update('tl_module', [$this->dbFallback => $content], ['id' => $moduleId]);
        }
    }
}
