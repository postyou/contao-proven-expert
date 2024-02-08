<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-proven-expert.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoProvenExpert\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\ModuleModel;
use Contao\StringUtil;
use Contao\Template;
use Doctrine\DBAL\Connection;
use Postyou\ContaoProvenExpert\ApiClient\ProvenExpertApiClient;
use Postyou\ContaoProvenExpert\Cache\ProvenExpertCacheItem;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @FrontendModule(category="miscellaneous")
 */
class ProvenExpertRichSnippet extends AbstractFrontendModuleController
{
    public const TYPE = 'proven_expert_rich_snippet';

    public function __construct(
        private readonly ProvenExpertApiClient $peApiClient,
        private readonly TagAwareAdapterInterface $peCache,
        private readonly Connection $db,
    ) {}

    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {
        $page = $this->getPageModel();

        if (!$page) {
            return new Response();
        }

        $page->loadDetails();

        if (empty($page->peUploadDirectory)) {
            return new Response();
        }

        $peCacheItem = new ProvenExpertCacheItem($this->peCache, $page, $model);

        if (!$peCacheItem->isHit()) {
            $html = $this->getHtml($model);

            $peCacheItem->set($html);

            $this->db->update('tl_module', ['peHtml' => $html], ['id' => (int) $model->id]);
        }

        $template->peHtml = $peCacheItem->get();

        return $template->getResponse();
    }

    private function getHtml(ModuleModel $model): string
    {
        $options = StringUtil::deserialize($model->peWidgetOptions, true);
        $options = array_combine(array_column($options, 'key'), array_column($options, 'value'));

        // Fetch the widget
        $response = $this->peApiClient->getRichsnippet($options);

        return $response['html'];
    }
}
