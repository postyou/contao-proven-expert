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
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Postyou\ContaoProvenExpert\ApiClient\ProvenExpertApiClient;
use Postyou\ContaoProvenExpert\Cache\ProvenExpertCacheItem;
use Postyou\ContaoProvenExpert\Util\WidgetUtil;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule(template: 'mod_proven_expert_widget')]
class ProvenExpertWidget extends AbstractFrontendModuleController
{
    public const TYPE = 'proven_expert_widget';

    public function __construct(
        private readonly ProvenExpertApiClient $peApiClient,
        private readonly TagAwareAdapterInterface $peCache,
        private readonly WidgetUtil $widgetUtil,
        private readonly Connection $db,
    ) {}

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        $page = $this->getPageModel();

        if (null === $page) {
            return new Response();
        }

        $page->loadDetails();

        if (empty($page->peUploadDirectory)) {
            return new Response();
        }

        $peCacheItem = new ProvenExpertCacheItem($this->peCache, $page->rootId, $model->id);

        if (!$peCacheItem->isHit()) {
            $html = $this->getHtml($page, $model);

            if (!empty($html)) {
                $peCacheItem->set($html);
                $this->db->update('tl_module', ['peHtml' => $html], ['id' => (int) $model->id]);
            }
        }

        // Get either the cached version or the db fallback.
        $template->peHtml = $peCacheItem->get() ?: $model->peHtml;

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
