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

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\PageModel;
use Postyou\ContaoProvenExpert\ApiClient\ProvenExpertApiClient;

#[AsHook('loadPageDetails')]
class LoadPageDetailsListener
{
    public function __construct(
        private readonly ProvenExpertApiClient $apiClient,
    ) {}

    /**
     * @param array<PageModel> $parents
     */
    public function __invoke(array $parents, PageModel $page): void
    {
        if ([] === $parents) {
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

        if ('' !== $root->peApiId && '' !== $root->peApiKey) {
            // @phpstan-ignore-next-line (wrong argument.type)
            $this->apiClient->setCredentials([$root->peApiId, $root->peApiKey]);
        }

        $page->peUploadDirectory = $root->peUploadDirectory;
    }
}
