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
use Postyou\ContaoProvenExpert\ApiClient\ProvenExpertApiClient;
use Postyou\ContaoProvenExpert\Cache\CacheableResponse;
use Postyou\ContaoProvenExpert\Util\WidgetUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule(template: 'mod_proven_expert_widget')]
class ProvenExpertWidget extends AbstractFrontendModuleController
{
    public const TYPE = 'proven_expert_widget';

    public function __construct(
        private readonly ProvenExpertApiClient $peApiClient,
        private readonly CacheableResponse $cacheableResponse,
        private readonly WidgetUtil $widgetUtil,
    ) {}

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        if (null === ($page = $this->getPageModel())) {
            return new Response();
        }

        return $this->cacheableResponse
            ->setContext($page, $model)
            ->getResponse($template, fn () => $this->getContent($page, $model))
        ;
    }

    private function getContent(PageModel $page, ModuleModel $model): string
    {
        if ('custom' === $model->peWidgetType) {
            $html = $model->html;

            $this->widgetUtil->downloadImageSrc($html, $page, $model->id);

            return $html;
        }

        /** @var array<array{key: string, value: int|string}> */
        $options = StringUtil::deserialize($model->peWidgetOptions, true);

        $options = array_combine(array_column($options, 'key'), array_column($options, 'value'));

        $options['type'] = $model->peWidgetType;

        if (\in_array($model->peWidgetType, ['portrait', 'square', 'landscape', 'circle', 'logo'], true)) {
            $options['width'] = $model->peWidgetWidth;
        }

        /** @var array<string, int|string> $options */
        $response = $this->peApiClient->createWidget($options);

        $html = \is_array($response['html']) ? implode('', $response['html']) : $response['html'];

        $this->widgetUtil->downloadImageSrc($html, $page, $model->id);

        return $html;
    }
}
