<?php

namespace APP\plugins\generic\podcast;

use APP\facades\Repo;
use APP\submission\Collector;
use APP\submission\Submission;
use APP\file\PublicFileManager;
use PKP\services\PKPFileService;
use APP\template\TemplateManager;
use PKP\core\Registry;
use PKP\plugins\GatewayPlugin;
use APP\plugins\generic\podcast\classes\enums\ItunesCategory;
use APP\plugins\generic\podcast\classes\enums\ItunesSubCategory;

require_once(dirname(__FILE__) . '/vendor/autoload.php');

class PodcastFeedGatewayPlugin extends GatewayPlugin
{
    public function __construct(
        protected PodcastPlugin $podcastPlugin,
    ) {
        parent::__construct();
    }

    public function getName(): string {
        return 'PodcastFeedGatewayPlugin';
    }

    public function getHideManagement(): bool {
        return true;
    }

    public function getDisplayName(): string {
        return __('plugins.generic.podcast.displayName');
    }

    public function getDescription(): string {
        return __('plugins.generic.podcast.description');
    }

    public function getPluginPath() {
        return $this->podcastPlugin->getPluginPath();
    }

    public function getEnabled() {
        return $this->podcastPlugin->getEnabled();
    }

    public function fetch($args, $request)
    {
        $journal = $request->getContext();
        if (!$journal || !$this->podcastPlugin->getEnabled($journal->getId())) {
            return false;
        }
        $publicFileManager = new PublicFileManager();
        $fileService = new PKPFileService();
        $journalUrl = $request->url($journal->getPath());
        $journalPublicFilesUrl = $request->getBaseUrl() . '/' . $publicFileManager->getContextFilesPath($journal->getId());
        $rssLocale = $journal->getPrimaryLocale();
        $feedUrl = $request->url(
            null,
            'gateway',
            'plugin',
            ['PodcastFeedGatewayPlugin'],
        );

        $itunesCategories = [];
        $journalItunesCategories = $journal->getData('itunesCategories');
        if (is_array($journalItunesCategories) ** !empty($journalItunesCategories)) {
            foreach(ItunesCategory::cases() as $itunesCategory) {
                $itunesSubcategories = array_filter($itunesCategory->subcategories(), function ($itunesSubcategory) use($journalItunesCategories) {
                    return in_array($itunesSubcategory->value, $journalItunesCategories);
                });
                $itunesSubcategories = array_map(function ($itunesSubcategory) {
                    return $itunesSubcategory->label();
                }, $itunesSubcategories);

                if (count($itunesSubcategories) > 0 || in_array($itunesCategory->value, $journalItunesCategories)) {
                    $itunesCategories[]= [
                        'label' => $itunesCategory->label(),
                        'subcategories' => $itunesSubcategories,
                    ];
                }
            }
        }

        $issues = Repo::issue()->getCollector()
            ->filterByContextIds([$journal->getId()])
            ->filterByPublished(true)
            ->getMany();
        $issuesMap = [];
        foreach ($issues as $issue) {
            $issuesMap[$issue->getId()] = $issue;
        }

        $submissions = Repo::submission()->getCollector()
            ->filterByContextIds([$journal->getId()])
            ->filterByStatus([Submission::STATUS_PUBLISHED])
            ->orderBy(Collector::ORDERBY_LAST_MODIFIED, Collector::ORDER_DIR_DESC)
            ->getMany();
        $items = [];
        $journalAuthors = [];
        foreach ($submissions as $submission) {
            $publication = $submission->getCurrentPublication();
            if (!$publication) {
                continue;
            }
            $issue = $issuesMap[$publication->getIssueId()] ?? null;
            if (!$issue) {
                continue;
            }

            $galleys = Repo::galley()->getCollector()
                ->filterByContextIds([$journal->getId()])
                ->filterByPublicationIds([$publication->getId()])
                ->getMany()
                ->toArray();
            $audioGalleys = array_filter($galleys, function ($galley) {
                return str_starts_with($galley->getFileType(), 'audio/');
            });
            $audioGalley = array_shift($audioGalleys);
            if (is_null($audioGalley)) {
                continue;
            }
            $audioFilesize = $fileService->fs->fileSize($audioGalley->getFile()->getData('path'));

            $transcriptGalleys = array_filter($galleys, function ($galley) {
                return in_array($galley->getFileType(), ['text/plain', 'text/html', 'text/vtt', 'application/json', 'application/x-subrip']);
            });
            $imageGalleys = array_filter($galleys, function ($galley) {
                return str_starts_with($galley->getFileType(), 'image/');
            });

            // Build author keywords
            $publicationAuthors = [];
            foreach ($publication->getData('authors') as $author) {
                if (!array_key_exists($author->getId(), $journalAuthors)) {
                    $journalAuthors[$author->getId()] = $author->getFullName();
                }
                $publicationAuthors []= $author->getFullName();
            }
            $duration = $publication->getData('duration') ?? null;
            $durationInSeconds = null;
            if (!is_null($duration)) {
                $durationParts = explode(':', $duration);
                if (count($durationParts) <= 3) {
                    $durationInSeconds = intval(array_pop($durationParts) ?? 0);
                    $durationInSeconds += 60 * intval(array_pop($durationParts) ?? 0);
                    $durationInSeconds += 60 * 60 * intval(array_pop($durationParts) ?? 0);
                }
            }

            $items[]= [
                'issue' => $issue,
                'submission' => $submission,
                'publication' => $publication,
                'audioGalley' => $audioGalley,
                'audioFilesize' => $audioFilesize,
                'transcriptGalleys' => $transcriptGalleys,
                'imageGalleys' => $imageGalleys,
                'publicationAuthors' => $publicationAuthors,
                'durationInSeconds' => $durationInSeconds,
            ];
        }

        TemplateManager::getManager($request)
            ->assign(
                [
                    'journal' => $journal,
                    'journalUrl' => $journalUrl,
                    'journalPublicFilesUrl' => $journalPublicFilesUrl,
                    'journalAuthors' => $journalAuthors,
                    'locale' => $rssLocale,
                    'items' => $items,
                    'itunesCategories' => $itunesCategories,
                    'feedUrl' => $feedUrl,
                    'applicationVersion' => Registry::get('appVersion'),
                    'pluginVersion' => $this->podcastPlugin->getCurrentVersion()->getVersionString(),
                    'latestDate' => $submissions->first()?->getData('lastModified'),
                ]
            )
            ->setHeaders(['content-type: application/rss+xml; charset=utf-8'])
            ->display($this->podcastPlugin->getTemplateResource("podcast_rss.tpl"));

        return true;
    }
}

// For backwards compatibility -- expect this to be removed approx. OJS/OMP/OPS 3.6
if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\generic\podcast\PodcastFeedGatewayPlugin', '\PodcastFeedGatewayPlugin');
}