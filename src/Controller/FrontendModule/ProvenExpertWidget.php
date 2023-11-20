<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-proven-expert
 *
 * (c) POSTYOU Digital- & Filmagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoProvenExpert\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\Template;
use Doctrine\DBAL\Connection;
use Postyou\ContaoProvenExpert\ApiClient\ProvenExpertApiClient;
use Postyou\ContaoProvenExpert\Cache\ProvenExpertCacheItem;
use Postyou\ContaoProvenExpert\Util\WidgetUtil;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @FrontendModule(category="miscellaneous")
 */
class ProvenExpertWidget extends AbstractFrontendModuleController
{
    public const TYPE = 'proven_expert_widget';

    private $peApiClient;
    private $peCache;
    private $widgetUtil;
    private $db;

    public function __construct(ProvenExpertApiClient $peApiClient, TagAwareAdapterInterface $peCache, WidgetUtil $widgetUtil, Connection $db)
    {
        $this->peApiClient = $peApiClient;
        $this->peCache = $peCache;
        $this->widgetUtil = $widgetUtil;
        $this->db = $db;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {
        $page = $this->getPageModel();

        if (null === $page) {
            return new Response();
        }

        $page->loadDetails();

        if (empty($page->peUploadDirectory)) {
            return new Response();
        }

        $peCacheItem = new ProvenExpertCacheItem($this->peCache, $page, $model);

        if (!$peCacheItem->isHit()) {
            $html = $this->getHtml($page, $model);

            $peCacheItem->set($html);

            $this->db->update('tl_module', ['peHtml' => $html], ['id' => (int) $model->id]);
        }

        $template->peHtml = $peCacheItem->get();

        return $template->getResponse();
    }

    private function getHtml(PageModel $page, ModuleModel $model): string
    {
        if ('custom' === $model->peWidgetType) {
            $html = $model->html;

            $this->widgetUtil->downloadImageSrc($html, $page, $model->id);

            return $html;
        }

        $options = StringUtil::deserialize($model->peWidgetOptions, true);
        $options = array_combine(array_column($options, 'key'), array_column($options, 'value'));

        $options['type'] = $model->peWidgetType;

        if (\in_array($model->peWidgetType, ['portrait', 'square', 'landscape', 'circle', 'logo'], true)) {
            $options['width'] = $model->peWidgetWidth;
        }

        // Fetch the widget
        $response = $this->peApiClient->createWidget($options);

        $html = \is_array($response['html']) ? implode('', $response['html']) : $response['html'];

        $this->widgetUtil->downloadImageSrc($html, $page, $model->id);

        return $html;
    }
}
