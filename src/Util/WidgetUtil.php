<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-proven-expert.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoProvenExpert\Util;

use Contao\Config;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\File;
use Contao\FilesModel;
use Contao\PageModel;
use Contao\StringUtil;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WidgetUtil
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly LoggerInterface $logger,
        #[Autowire('%kernel.debug%')]
        private readonly bool $debug,
    ) {}

    public function moveStylesToHead(string $html): string
    {
        if (preg_match_all('/<style[\s\S]+?<\/style>/s', $html, $matches)) {
            foreach ($matches[0] as $match) {
                $html = str_replace($match, '', $html);
                $GLOBALS['TL_HEAD'][] = $match;
            }
        }

        return $html;
    }

    public function downloadImageSrc(string &$html, PageModel $page, int $modelId): void
    {
        /** @phpstan-ignore-next-line (wrong argument.type) */
        $uuid = StringUtil::binToUuid($page->peUploadDirectory);
        $folder = FilesModel::findByUuid($uuid);

        if (null === $folder) {
            throw new \RuntimeException(sprintf('Could not find the configured folder in page %s for ProvenExpert Image downloads.', $page->id));
        }

        $filePath = $folder->path.'/pe_'.$page->rootId.'_widget_'.$modelId;

        preg_match_all('/<img\s+.*?src\s*=\s*([\'"])(.*?)\1/i', $html, $matches);

        foreach (array_unique($matches[2]) as $k => $src) {
            $localSrc = $this->downloadFile($src, $filePath.'_'.$k);
            $html = str_replace($src, $localSrc, $html);
        }
    }

    private function downloadFile(string $url, string $path): string
    {
        $response = $this->client->request('GET', $url);

        try {
            $image = $response->getContent(true);
        } catch (ExceptionInterface $e) {
            if ($this->debug) {
                // Rethrow the exception in debug mode
                throw $e;
            }

            $this->logger->error(
                'ProvenExpert image download: '.$e->getMessage().' Try activating the debug mode for more details.',
                ['contao' => new ContaoContext(__METHOD__, ContaoContext::ERROR)]
            );

            return '';
        }

        $ext = strtok(pathinfo($url, PATHINFO_EXTENSION), '?');
        $file = new File($path.'.'.$ext);

        $file->write($image);

        $file->close();

        return $file->path;
    }
}
