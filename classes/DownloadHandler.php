<?php

namespace APP\plugins\generic\podcast\classes;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use APP\plugins\generic\podcast\PodcastPlugin;
use PKP\config\Config;
use PKP\plugins\Hook;
use APP\core\Application;

class DownloadHandler
{
    public function __construct(
        public PodcastPlugin $plugin
    ) {}

    public function xSendFileDownload(string $hookName, array $args): bool
    {
        $file = $args[0];
        $filename = $args[1];
        $inline = $args[2] ?? false;

        # HACK: inline value is never passed to download hook properly, instead manually check the query params
        $request = Application::get()->getRequest();
        $queryParams = $request->getQueryArray();
        if (array_key_exists('inline', $queryParams)) {
            $inline = in_array($queryParams['inline'] ?? '', ['1', 'true']);
        }

        $filesDir = Config::getVar('files', 'files_dir');
        $filePath = "{$filesDir}/{$file->path}";
        $mimetype = $file->mimetype ?? 'application/octet-stream';

        $response = new BinaryFileResponse($filePath);
        $response->headers->set('Content-Type', $mimetype);
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->setContentDisposition($inline ? ResponseHeaderBag::DISPOSITION_INLINE : ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->trustXSendfileTypeHeader();
        $response->send();

        return HOOK::ABORT;
    }
}