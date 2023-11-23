<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-proven-expert
 *
 * (c) POSTYOU Digital- & Filmagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoProvenExpert\Util;

use Contao\File;
use Contao\FilesModel;
use Contao\PageModel;
use Contao\StringUtil;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WidgetUtil
{
    /** @var HttpClientInterface */
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function downloadImageSrc(string &$html, PageModel $page, $modelId): void
    {
        $uuid = StringUtil::binToUuid($page->peUploadDirectory);
        $folder = FilesModel::findByUuid($uuid);
        $filePath = $folder->path.'/pe_'.$page->rootId.'_widget_'.$modelId;

        preg_match_all('/<img\s+.*?src\s*=\s*([\'"])(.*?)\1/i', $html, $matches);

        foreach (array_unique($matches[2]) as $k => $src) {
            $localSrc = $this->downloadFile($src, $filePath.'_'.$k);
            $html = str_replace($src, $localSrc, $html);
        }
    }

    private function downloadFile(string $url, string $path): string
    {
        $ext = strtok(pathinfo($url, PATHINFO_EXTENSION), '?');
        $file = new File($path.'.'.$ext);

        $response = $this->client->request('GET', $url);

        $file->write($response->getContent());

        $file->close();

        return $file->path;
    }
}
