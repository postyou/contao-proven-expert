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

use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use Contao\PageModel;
use Doctrine\DBAL\Connection;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;

class CacheableResponse
{
    private ?PageModel $pageModel;
    private ?ModuleModel $model;

    public function __construct(
        #[Autowire(service: 'contao_proven_expert.cache')]
        private readonly TagAwareAdapterInterface $cache,
        private readonly Connection $db,
    ) {}

    public function setContext(PageModel $pageModel, ModuleModel $model): self
    {
        $this->pageModel = $pageModel;
        $this->model = $model;

        return $this;
    }

    /**
     * @param \Closure(): string $getContent
     */
    public function getResponse(FragmentTemplate $template, \Closure $getContent): Response
    {
        if (null === $this->pageModel || null === $this->model) {
            throw new \LogicException('You have to call setContext first.');
        }

        $this->pageModel->loadDetails();

        if (empty($this->pageModel->peUploadDirectory)) {
            return new Response();
        }

        $key = implode('.', [ProvenExpertCache::NAMESPACE, $this->pageModel->rootId, $this->model->id]);
        $cacheItem = $this->cache->getItem($key);

        if (!$cacheItem->isHit()) {
            $html = $getContent();

            if (!empty($html)) {
                $cacheItem
                    ->set($html)
                    ->expiresAfter(\DateInterval::createFromDateString('1 hour'))
                    ->tag([
                        ProvenExpertCache::NAMESPACE,
                        ProvenExpertCache::moduleTag($this->model->id),
                    ])
                ;

                $this->cache->save($cacheItem);

                $this->db->update('tl_module', ['peHtml' => $html], ['id' => (int) $this->model->id]);
            }
        }

        // Get either the cached version or the db fallback.
        $template->peHtml = $cacheItem->get() ?: $this->model->peHtml;

        return $template->getResponse();
    }
}
