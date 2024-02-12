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
use Contao\StringUtil;
use Postyou\ContaoProvenExpert\ApiClient\ProvenExpertApiClient;
use Postyou\ContaoProvenExpert\Cache\CacheableContent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule]
class ProvenExpertRichSnippet extends AbstractFrontendModuleController
{
    public const TYPE = 'proven_expert_rich_snippet';

    public function __construct(
        private readonly ProvenExpertApiClient $peApiClient,
        private readonly CacheableContent $cacheableContent,
    ) {}

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        if (null === ($page = $this->getPageModel())) {
            return new Response();
        }

        $template->peHtml = $this->cacheableContent
            ->setContext($page, $model)
            ->setDbFallback('peHtml')
            ->getResult(fn () => $this->getContent($model))
        ;

        return $template->getResponse();
    }

    private function getContent(ModuleModel $model): string
    {
        /** @var array<array{key: string, value: int|string}> $options */
        $options = StringUtil::deserialize($model->peWidgetOptions, true);
        $options = array_combine(array_column($options, 'key'), array_column($options, 'value'));

        // Fetch the widget
        $response = $this->peApiClient->getRichsnippet($options);

        return $response['html'];
    }
}
