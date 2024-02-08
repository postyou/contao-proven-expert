<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-proven-expert.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoProvenExpert\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\PageModel;
use Postyou\ContaoProvenExpert\ApiClient\ProvenExpertApiClient;

/**
 * @Hook("loadPageDetails")
 */
class LoadPageDetailsListener
{
    /** @var ProvenExpertApiClient */
    private $provider;

    public function __construct(ProvenExpertApiClient $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param PageModel[] $parents
     */
    public function __invoke(array $parents, PageModel $page): void
    {
        if (empty($parents)) {
            return;
        }

        $root = end($parents);

        if (!$root->fallback) {
            $t = PageModel::getTable();
            $root = PageModel::findOneBy(["{$t}.dns = ?", "{$t}.fallback = '1'"], [$root->dns]) ?? $root;
        }

        if (empty($root->usePeApi)) {
            return;
        }

        if (!empty($root->peApiId) && !empty($root->peApiKey)) {
            $this->provider->setCredentials([$root->peApiId, $root->peApiKey]);
        }

        $page->peUploadDirectory = $root->peUploadDirectory;
    }
}
