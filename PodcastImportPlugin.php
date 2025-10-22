<?php

namespace APP\plugins\generic\podcast;

use Exception;
use PKP\core\Core;
use PKP\plugins\ImportExportPlugin;
use PKP\db\DAORegistry;
use PKP\user\User;
use PKP\file\TemporaryFileManager;
use PKP\submission\GenreDAO;
use PKP\submissionFile\SubmissionFile;
use PKP\userGroup\UserGroup;
use PKP\security\Role;
use PKP\galley\Galley;
use APP\core\Application;
use APP\file\PublicFileManager;
use PKP\config\Config;
use APP\facades\Repo;
use APP\journal\Journal;
use APP\journal\JournalDAO;
use APP\issue\Issue;
use APP\submission\Submission;
use APP\publication\Publication;
use APP\core\Services;
use SimplePie\Item as SimplePieItem;
use SimplePie\SimplePie;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use APP\plugins\generic\podcast\classes\enums\EpisodeType;
use APP\plugins\generic\podcast\classes\enums\ItunesCategory;
use APP\plugins\generic\podcast\classes\enums\ItunesSubCategory;

require_once(dirname(__FILE__) . '/vendor/autoload.php');

class PodcastImportPlugin extends ImportExportPlugin
{
    protected const NS_GOOGLE_PLAY = 'http://www.google.com/schemas/play-podcasts/1.0';
    protected const NS_PODCAST = 'https://podcastindex.org/namespace/1.0';

    public Client $client;
    public SimplePie $feed;
    public TemporaryFileManager $temporaryFileManager;
    public PublicFileManager $publicFileManager;
    public GenreDAO $genreDao;
    public JournalDAO $journalDao;

    public function __construct(
        protected PodcastPlugin $podcastPlugin,
        protected ?string $locale = null,
        protected ?Journal $journal = null,
        protected ?User $adminUser = null,
        protected ?string $rssFeedUrl = null,
        protected array $tempFiles = [],
        protected array $issues = [],
        protected array $episodeCounter = [],
        protected array $podcastKeywords = [],
        protected array $submissions = [],
        protected ?int $submissionGenreId = null,
        protected ?int $transcriptGenreId = null,
        protected ?int $imageGenreId = null,
        protected ?int $otherGenreId = null,
        protected ?int $defaultSectionId = null,
    ) {
        parent::__construct();

        $this->client = new Client();
        $this->feed = new SimplePie();
        $this->temporaryFileManager = new TemporaryFileManager();
        $this->publicFileManager = new PublicFileManager();
        $this->genreDao = DAORegistry::getDAO('GenreDAO');
        $this->journalDao = DAORegistry::getDAO('JournalDAO');
    }

    public function register($category, $path, $mainContextId = null): bool
    {
        $success = parent::register($category, $path);
        // $this->addLocaleData();
        if ($success && $this->getEnabled()) {
        }
        return $success;
    }

    public function getInstallSitePluginSettingsFile()
    {
        return $this->podcastPlugin->getPluginPath() . '/settings.xml';
    }

    public function getName(): string
    {
        return 'PodcastImportPlugin';
    }

    public function getDisplayName(): string
    {
        return 'Podcast Import Plugin';
    }

    public function getDescription(): string
    {
        return 'Import Podcast episodes and metadata from RSS feeds.';
    }

    public function usage($scriptName)
    {
        echo "Usage: \n";
        echo "{$scriptName} {$this->getName()} --journal-id=<JOURNAL ID> --rss-url=<RSS FEED URL> --add-contributor-roles\n";
        echo "{$scriptName} {$this->getName()} --journal-path=<JOURNAL PATH> --rss-url=<RSS FEED URL> --add-contributor-roles\n";
        echo "--journal-id=<JOURNAL ID>: You MUST provide a journal id\n";
        echo "--journal-path=<JOURNAL PATH>: You MUST provide a journal path\n";
        echo "--rss-url=<RSS FEED URL>: You MUST provide an rss url on the first import. The `--rss-url` option will be ignored on subsequent imports.\n";
        echo "--add-contributor-roles: You can optionally add some common podcast marc relator contributor roles (Host, Producer, Interviewer, Interviewee, Audio Producer, and Contributor).\n";
    }

    protected function getFeedTag(string $namespace, string $name) : ?array {
        $tags = $this->feed->get_channel_tags($namespace, $name);
        if ($tags && count($tags)) {
            return $tags[0];
        }

        return null;
    }

    protected function getFeedTagValue(string $namespace, string $name) : ?string {
        $tag = $this->getFeedTag($namespace, $name);
        if ($tag && array_key_exists('data', $tag)) {
            return $this->feed->sanitize($tag['data'], SimplePie::CONSTRUCT_TEXT);
        }

        return null;
    }

    protected function getItemTag(SimplePieItem $item, string $namespace, string $name) : ?array {
        $tags = $item->get_item_tags($namespace, $name);
        if ($tags && count($tags)) {
            return $tags[0];
        }

        return null;
    }

    protected function getItemTagValue(SimplePieItem $item, string $namespace, string $name) : ?string {
        $tag = $this->getItemTag($item, $namespace, $name);
        if ($tag && array_key_exists('data', $tag)) {
            return $this->feed->sanitize($tag['data'], SimplePie::CONSTRUCT_TEXT);
        }

        return null;
    }

    public function executeCLI($scriptName, &$args): void {
        if (!$this->podcastPlugin->getEnabled()) {
            throw new Exception('Error: PodcastPlugin must be enabled to import Podcasts.');
        }

        $journalId = $journalPath = $this->rssFeedUrl = null;
        $addContributorRoles = false;
        foreach($args as $arg) {
            if (str_starts_with($arg, '--journal-id=')) {
                $journalId = substr($arg, strlen('--journal-id='));
                $journalId = is_numeric($journalId) ? (int) $journalId : null;
            } elseif (str_starts_with($arg, '--journal-path=')) {
                $journalPath = substr($arg, strlen('--journal-path='));
                $journalPath = '' === trim($journalPath) ? null : $journalPath;
            } elseif (str_starts_with($arg, '--rss-url=')) {
                $this->rssFeedUrl = substr($arg, strlen('--rss-url='));
                $this->rssFeedUrl = '' === trim($this->rssFeedUrl) ? null : $this->rssFeedUrl;
            } elseif (str_starts_with($arg, '--add-contributor-roles')) {
                $addContributorRoles = true;
            }
        }

        if (is_null($journalId) && is_null($journalPath)) {
            throw new Exception("Error: `--journal-id=<JOURNAL ID>` or `--journal-path=<JOURNAL PATH>` is required.");
        }

        if (!is_null($journalId)) {
            if (!$this->journalDao->exists($journalId)) {
                throw new Exception("Error: Invalid `--journal-id=<JOURNAL ID>`. No journal found matching the ID {$journalId}.\n");
            }
            $this->journal = $this->journalDao->getById($journalId);
        } else {
            if (!$this->journalDao->existsByPath($journalPath)) {
                throw new Exception("Error: Invalid `--journal-path=<JOURNAL PATH>`. No journal found matching the path {$journalPath}.\n");
            }
            $this->journal = $this->journalDao->getByPath($journalPath);
        }

        if (!is_null($this->journal->getData('rssFeed'))) {
            $this->rssFeedUrl = $this->journal->getData('rssFeed');
        }
        if (is_null($this->rssFeedUrl)) {
            throw new Exception("Error: Invalid `--rss-url=<RSS FEED URL>`. You must provide an rss feed url when first importing the Podcast data.\n");
        }

        $adminUsers = Repo::user()->getAdminUsers();
        if ($adminUsers->isNotEmpty()) {
            $this->adminUser = $adminUsers->first();
        } else {
            throw new Exception("Error: No admin users found for OJS install. An admin user is required for adding the files automatically.\n");
        }

        echo "Fetching RSS feed from {$this->rssFeedUrl}\n";
        try {
            $response = $this->client->get($this->rssFeedUrl);
        } catch (ConnectException $e) {
            echo "Could not connect to RSS feed url.\n";
            echo "Message: {$e->getMessage()}\n";
            return;
        } catch (RequestException $e) {
            echo "Error accessing RSS feed. Error: {$e->getMessage()}\n";
            return;
        }
        $this->feed->set_raw_data($response->getBody()->getContents());
        $this->feed->init();

        // Try to use Podcast RSS locale if set and supported. otherwise use the journal's primary/default locale as fallback
        $rssLanguageCode = $this->feed->get_language();
        if ($rssLanguageCode) {
            $supportedLocales = $this->journal->getSupportedFormLocales();
            foreach ($supportedLocales as $localeKey) {
                if (in_array($rssLanguageCode, [$localeKey, mb_substr($localeKey, 0, 2), mb_substr($localeKey, 0, 5)]) ) {
                    $this->locale = $localeKey;
                }
            }
        }
        if (is_null($this->locale)) {
            $this->locale = $this->journal->getPrimaryLocale();
        }

        $submissionGenre = $this->genreDao->getByKey('SUBMISSION', $this->journal->getId());
        $this->submissionGenreId = !is_null($submissionGenre) ? $submissionGenre->getId() : null;
        $transcriptGenre = $this->genreDao->getByKey('TRANSCRIPTS', $this->journal->getId());
        $this->transcriptGenreId = !is_null($transcriptGenre) ? $transcriptGenre->getId() : null;
        $imageGenre = $this->genreDao->getByKey('IMAGE', $this->journal->getId());
        $this->imageGenreId = !is_null($imageGenre) ? $imageGenre->getId() : null;
        $otherGenre = $this->genreDao->getByKey('OTHER', $this->journal->getId());
        $this->otherGenreId = !is_null($otherGenre) ? $otherGenre->getId() : null;

        // get first section (try active sections first then fallback to any section)
        $firstActiveSection = REPO::section()
            ->getCollector()
            ->filterByContextIds([$this->journal->getId()])
            ->excludeInactive(true)
            ->getMany()
            ->first();
        $firstSection = $firstActiveSection ?? REPO::section()
            ->getCollector()
            ->filterByContextIds([$this->journal->getId()])
            ->getMany()
            ->first();
        $this->defaultSectionId = !is_null($firstSection) ? $firstSection->getId() : null;

        $this->processPodcast();
        $this->processPodcastSeasons();
        $this->processPodcastEpisodes();
        if ($addContributorRoles) {
            $this->addContributorRoles();
        }
        $this->cleanup();
    }

    protected function downloadFileToTemp(string $fileUrl): ?string {
        $originalFileName = basename(parse_url($fileUrl, PHP_URL_PATH));
        $tempFilePath = basename(@tempnam($this->temporaryFileManager->getBasePath(), 'PodcastImportPlugin_') . '_' . $originalFileName);
        try {
            echo "Downloading `{$fileUrl}` to temp dir `{$tempFilePath}`\n";
            $response = $this->client->get($fileUrl, [
                'sink' => $tempFilePath,
                'headers' => [
                    'Accept-Encoding' => 'gzip, deflate, br',
                ],
            ]);
        } catch (ConnectException $e) {
            echo "Could not download file $fileUrl.\n";
            echo "Message: {$e->getMessage()}\n";
            return null;
        } catch (RequestException $e) {
            echo "Error downloading file $fileUrl. Error: {$e->getMessage()}\n";
            return null;
        }
        $this->tempFiles[$fileUrl] = $tempFilePath;
        return $tempFilePath;
    }

    protected function processPodcast() : void {
        echo "Processing Podcast metadata\n";

        $this->journal->setData('rssFeed', $this->rssFeedUrl);
        if (!in_array($this->journal->getData('keywords'), [$this->journal::METADATA_REQUEST, $this->journal::METADATA_REQUIRE])) {
            $this->journal->setData('keywords', $this->journal::METADATA_REQUEST);
        }
        if (!in_array($this->journal->getData('subjects'), [$this->journal::METADATA_REQUEST, $this->journal::METADATA_REQUIRE])) {
            $this->journal->setData('subjects', $this->journal::METADATA_REQUEST);
        }
        if (!in_array($this->journal->getData('agencies'), [$this->journal::METADATA_REQUEST, $this->journal::METADATA_REQUIRE])) {
            $this->journal->setData('agencies', $this->journal::METADATA_REQUEST);
        }
        if (!in_array($this->journal->getData('coverage'), [$this->journal::METADATA_REQUEST, $this->journal::METADATA_REQUIRE])) {
            $this->journal->setData('coverage', $this->journal::METADATA_REQUEST);
        }
        if (!in_array($this->journal->getData('rights'), [$this->journal::METADATA_REQUEST, $this->journal::METADATA_REQUIRE])) {
            $this->journal->setData('rights', $this->journal::METADATA_REQUEST);
        }
        if (!in_array($this->journal->getData('source'), [$this->journal::METADATA_REQUEST, $this->journal::METADATA_REQUIRE])) {
            $this->journal->setData('source', $this->journal::METADATA_REQUEST);
        }
        if (!in_array($this->journal->getData('type'), [$this->journal::METADATA_REQUEST, $this->journal::METADATA_REQUIRE])) {
            $this->journal->setData('type', $this->journal::METADATA_REQUEST);
        }
        if (!in_array($this->journal->getData('type'), [$this->journal::METADATA_REQUEST, $this->journal::METADATA_REQUIRE])) {
            $this->journal->setData('type', $this->journal::METADATA_REQUEST);
        }
        if (is_null($this->journal->getData('copyrightHolderType'))) {
            $this->journal->setData('copyrightHolderType', 'author');
        }
        if (is_null($this->journal->getData('publishingMode'))) {
            $this->journal->setData('publishingMode', Journal::PUBLISHING_MODE_OPEN);
        }
        if (is_null($this->journal->getData('sidebar')) || empty($this->journal->getData('sidebar'))) {
            $this->journal->setData('sidebar', ['PodcastBlockPlugin']);
        }
        if ($this->locale != $this->journal->getPrimaryLocale()) {
            $this->journal->setPrimaryLocale($this->locale);
        }

        $guid = $this->getFeedTagValue(self::NS_PODCAST, 'guid') ?? Application::get()->getUUID();
        if ($guid && (is_null($this->journal->getData('rssGuid')) || '' === trim($this->journal->getData('rssGuid')))) {
            $this->journal->setData('rssGuid', $guid);
        }

        $title = $this->feed->get_title();
        if ($title && (is_null($this->journal->getName($this->locale)) || '' === trim($this->journal->getName($this->locale)))) {
            $this->journal->setName(html_entity_decode($title), $this->locale);
        }

        // TODO: ignore podcast subtitle? append add to name?
        // $subtitle = $this->getFeedTagValue(SimplePie::NAMESPACE_ITUNES, 'subtitle');
        // if ($subtitle && (is_null($this->journal->getData('subtitle')) || '' === trim($this->journal->getData('subtitle')) {
        //     $this->journal->setData('subtitle', mb_strimwidth(html_entity_decode($subtitle), 0, 252, '...'));
        // }

        $copyright = $this->feed->get_copyright();
        if ($copyright && (is_null($this->journal->getData('licenseTerms', $this->locale)) || '' === $this->journal->getData('licenseTerms', $this->locale))) {
            $this->journal->setData('licenseTerms', $copyright, $this->locale);
        }

        $explicitStr = $this->getFeedTagValue(SimplePie::NAMESPACE_ITUNES, 'explicit') ?? $this->getFeedTagValue(self::NS_GOOGLE_PLAY, 'explicit');
        if ($explicitStr && is_null($this->journal->getData('explicit'))) {
            $this->journal->setData('explicit', 'yes' === $explicitStr);
        }

        $description = $this->feed->get_description();
        if ($description && (is_null($this->journal->getDescription($this->locale)) || '' === trim($this->journal->getDescription($this->locale)))) {
            $this->journal->setDescription($description, $this->locale);
        }

        $podcastSource = $this->feed->get_link();
        if ($podcastSource && (is_null($this->journal->getData('podcastSource'))  || '' === $this->journal->getData('podcastSource'))) {
            $this->journal->setData('podcastSource', $podcastSource);
        }

        $categories = array_merge(
            $this->feed->get_channel_tags(SimplePie::NAMESPACE_ITUNES, 'category') ?? [],
            $this->feed->get_channel_tags(self::NS_GOOGLE_PLAY, 'category') ?? [],
        );
        if ($categories && count($categories) > 0 && empty($this->journal->getData('itunesCategories'))) {
            $itunesCategories = [];
            foreach ($categories as $categoryData) {
                $name = $this->feed->sanitize($categoryData['attribs']['']['text'], SimplePie::CONSTRUCT_TEXT);
                $itunesCategory = ItunesCategory::tryFrom(html_entity_decode($name));
                if ($itunesCategory) {
                    $itunesCategories[]=$itunesCategory->value;
                }

                if (isset($categoryData['child']) && is_array($categoryData['child'])) {
                    foreach ($categoryData['child'][SimplePie::NAMESPACE_ITUNES]['category'] as $subCategoryData) {
                        $subName = $this->feed->sanitize($subCategoryData['attribs']['']['text'], SimplePie::CONSTRUCT_TEXT);
                        $itunesSubCategory = ItunesSubCategory::tryFrom(html_entity_decode($subName));
                        if ($itunesSubCategory) {
                            $itunesCategories[]=$itunesSubCategory->value;
                        }
                    }
                }
            }
            $this->journal->setData('itunesCategories', $itunesCategories);
        }

        $keywordsString = trim($this->getFeedTagValue(SimplePie::NAMESPACE_ITUNES, 'keywords') ?? '');
        if ('' !== $keywordsString) {
            $keywords = explode(',', $keywordsString);
            foreach ($keywords as $keyword) {
                $this->podcastKeywords[] = trim($keyword);
            }
        }

        // add journal thumbnail (if thumbnail doesn't already exist)
        $coverImageUrl = $this->feed->get_image_url();
        if ($coverImageUrl && (is_null($this->journal->getData('journalThumbnail', $this->locale)) || is_null($this->journal->getData('pageHeaderLogoImage', $this->locale)))) {
            $coverImageFileName = basename(parse_url($coverImageUrl, PHP_URL_PATH));
            $extension = pathinfo($coverImageFileName, PATHINFO_EXTENSION);
            $coverImageTempPath = $this->tempFiles[$coverImageUrl] ?? $this->downloadFileToTemp($coverImageUrl);

            if (!is_null($coverImageTempPath)) {
                [$width, $height] = getimagesize($coverImageTempPath);
                if (is_null($this->journal->getData('journalThumbnail', $this->locale))) {
                    $publicFilename = "journalThumbnail_{$this->locale}.$extension";
                    echo "Saving journal thumbnail file to `{$publicFilename}`\n";
                    $this->publicFileManager->copyContextFile($this->journal->getId(), $coverImageTempPath, $publicFilename);
                    $this->journal->setData('journalThumbnail', [
                        'name' => $coverImageFileName,
                        'uploadName' => $publicFilename,
                        'width' => $width,
                        'height' => $height,
                        'dateUploaded' => Core::getCurrentDate(),
                        'altText' => '',
                    ], $this->locale);
                }
                if (is_null($this->journal->getData('pageHeaderLogoImage', $this->locale))) {
                    $publicFilename = "pageHeaderLogoImage_{$this->locale}.$extension";
                    echo "Saving journal Header Logo file to `{$publicFilename}`\n";
                    $this->publicFileManager->copyContextFile($this->journal->getId(), $coverImageTempPath, $publicFilename);
                    $this->journal->setData('pageHeaderLogoImage', [
                        'name' => $coverImageFileName,
                        'uploadName' => $publicFilename,
                        'width' => $width,
                        'height' => $height,
                        'dateUploaded' => Core::getCurrentDate(),
                        'altText' => '',
                    ], $this->locale);
                }
            }
        }

        // update journal context
        $this->journalDao->updateObject($this->journal);
        $this->journal = $this->journalDao->getById($this->journal->getId());

    }

    protected function processPodcastSeasons() : void {
        echo "Processing Season metadata\n";

        $existingIssues = Repo::issue()
            ->getCollector()
            ->filterByContextIds([$this->journal->getId()])
            ->getMany();
        foreach ($existingIssues as $issue) {
            if (!is_null($issue->getNumber())) {
                $this->issues[$issue->getNumber()] = $issue;
            }
        }
        $seasonNumbers = [];
        $issueEarliestPublished = [];
        foreach ($this->feed->get_items() as $item) {
            $seasonNumber = (string) ((int) ($this->getItemTagValue($item, SimplePie::NAMESPACE_ITUNES, 'season') ?? 1));
            $seasonNumbers = array_unique(array_merge($seasonNumbers, [$seasonNumber]));
            $seasonNumbers[$seasonNumber] = true;
            $publishedDate = $item->get_date('Y-m-d');
            if ($publishedDate) {
                // handle earliest date
                if (!array_key_exists($seasonNumber, $issueEarliestPublished)) {
                    $issueEarliestPublished[$seasonNumber] = $publishedDate;
                } else if (strtotime($publishedDate) < strtotime($issueEarliestPublished[$seasonNumber])) {
                    $issueEarliestPublished[$seasonNumber] = $publishedDate;
                }
            }
            $this->episodeCounter[$seasonNumber] = [
                'full' => 0,
                'bonus' => 0,
                'trailer' => 0,
            ];
        }
        sort($seasonNumbers, SORT_NUMERIC);
        foreach($seasonNumbers as $seasonNumber) {
            $issue = $this->issues[$seasonNumber] ?? null;

            // create new issue if issue with season number doesn't exist yet
            if (is_null($issue)) {
                echo "Generating new issue for Season {$seasonNumber}\n";
                $issue = new Issue();
                $issue->setJournalId($this->journal->getId());
                $issue->setShowVolume(false);
                $issue->setShowNumber(true);
                $issue->setNumber($seasonNumber);
                $issue->setShowYear(false);
                $issue->setPublished(true);
                $issue->setTitle("{$this->journal->getName($this->locale)} Season {$seasonNumber}", $this->locale);
                // $issue->setDescription('', $this->locale);

                // create issue
                $issueId = REPO::issue()->dao->insert($issue);
                $issue = REPO::issue()->get($issueId);
                $this->issues[$issue->getNumber()] = $issue;
            }

            // update date published if available
            if (array_key_exists($seasonNumber, $issueEarliestPublished)) {
                $issue->setDatePublished($issueEarliestPublished[$seasonNumber]);
            }

            // add issue coverImage (if coverImage doesn't already exist)
            $coverImageUrl = $this->feed->get_image_url();
            if ($coverImageUrl && is_null($issue->getCoverImage($this->locale))) {
                $coverImageFileName = basename(parse_url($coverImageUrl, PHP_URL_PATH));
                $extension = pathinfo($coverImageFileName, PATHINFO_EXTENSION);

                $coverImageTempPath = $this->tempFiles[$coverImageUrl] ?? $this->downloadFileToTemp($coverImageUrl);
                if (!is_null($coverImageTempPath)) {
                    $publicFilename = "cover_issue_{$issue->getId()}_{$this->locale}.$extension";
                    $this->publicFileManager->copyContextFile($this->journal->getId(), $coverImageTempPath, $publicFilename);
                    $issue->setCoverImage($publicFilename, $this->locale);
                }
            }

            // update issue
            REPO::issue()->dao->update($issue);
            $issue = REPO::issue()->get($issue->getId());
            $this->issues[$issue->getNumber()] = $issue;
        }

        // update journal currentIssue to latest season
        if (count($seasonNumbers) > 0) {
            $this->journal->setData('currentIssueId', $this->issues[end($seasonNumbers)]->getId());
            $this->journalDao->updateObject($this->journal);
            $this->journal = $this->journalDao->getById($this->journal->getId());
        }
    }

    protected function processPodcastEpisodes() : void {
        echo "Processing Episode metadata\n";

        $existingSubmissions = Repo::submission()
            ->getCollector()
            ->filterByContextIds([$this->journal->getId()])
            ->getMany();
        foreach ($existingSubmissions as $submission) {
            if (!is_null($submission->getData('importRssGuid'))) {
                $this->submissions[$submission->getData('importRssGuid')] = $submission;
            }
        }

        foreach (array_reverse($this->feed->get_items()) as $item) {
            $guid = $item->get_id();
            $seasonNumber = (string) ((int) ($this->getItemTagValue($item, SimplePie::NAMESPACE_ITUNES, 'season') ?? 1));
            echo "\nProcessing Episode guid {$guid}\n";
            $submission = $this->submissions[$guid] ?? null;
            $issue = $this->issues[$seasonNumber];

            if (is_null($submission)) {
                $submission = new Submission();
                $submission->setData('importRssGuid', $guid);
                $submission->setData('contextId', $this->journal->getId());
                $submission->setData('stageId', WORKFLOW_STAGE_ID_PRODUCTION);
                $submission->setData('status', Submission::STATUS_PUBLISHED);
                $submission->setData('submissionProgress', '');
                $submission->setData('locale', $this->locale);
                $submission->setData('dateLastActivity', Core::getCurrentDate());
                $submission->setData('dateSubmitted', Core::getCurrentDate());
                $submission->setData('dateModified', Core::getCurrentDate());

                $submissionId = REPO::submission()->dao->insert($submission);
                $submission = REPO::submission()->get($submissionId);
                $this->submissions[$guid] = $submission;
            }

            $publication = $submission->getCurrentPublication();
            if (is_null($publication)) {
                $publication = new Publication();
                $publication->setData('submissionId', $submission->getId());
                $publication->setIssueId($issue->getId());
                $publication->setData('status', 3);  // this wasn't working for some reason Publication::STATUS_PUBLISHED);
                $publication->setData('sectionId', $this->defaultSectionId);

                $publicationId = REPO::publication()->dao->insert($publication);
                $publication = REPO::publication()->get($publicationId);

                // update submission setting current publication to the new publication
                $submission->setData('currentPublicationId', $publication->getId());
                $submission->setData('dateModified', Core::getCurrentDate());
                REPO::submission()->dao->update($submission);
                $submission = REPO::submission()->get($submission->getId());
                $this->submissions[$guid] = $submission;
            }
            $publication = Repo::controlledVocab()->hydrateVocabsAsEntryData($publication);

            // include `contributors` + `contributor_role`?
            // we can get author, owner at the podcast level
            // we can also get author at the item/episode level
            // but we can't ensure we get all the required fields in OJS (ex: email) or separately the name properly
            // will be to be added manually after import

            $publication->setData('dateModified', Core::getCurrentDate());

            $episodeTypeStr = trim(mb_strtolower($this->getItemTagValue($item, SimplePie::NAMESPACE_ITUNES, 'episodeType') ?? ''));
            $episodeType = EpisodeType::tryFrom($episodeTypeStr) ?? EpisodeType::FULL;
            if (is_null($publication->getData('episodeType'))) {
                $publication->setData('episodeType', $episodeType->value);
            }

            $episodeNumber = $this->getItemTagValue($item, SimplePie::NAMESPACE_ITUNES, 'episode');
            if (!is_null($episodeNumber)) {
                $this->episodeCounter[$seasonNumber][$episodeType->value] = (int) $episodeNumber;
            } else {
                $episodeNumber = ++$this->episodeCounter[$seasonNumber][$episodeType->value];
            }
            if (is_null($publication->getData('episodeNumber'))) {
                $publication->setData('episodeNumber', $episodeNumber);
            } else {
                $episodeNumber = $publication->getData('episodeNumber');
            }
            echo "- Season {$seasonNumber} {$episodeType->label()} Episode {$episodeNumber}\n";

            $explicit = $this->getItemTagValue($item, SimplePie::NAMESPACE_ITUNES, 'explicit') ?? $this->getItemTagValue($item, self::NS_GOOGLE_PLAY, 'explicit');
            if (is_null($publication->getData('explicit'))) {
                $publication->setData('explicit', 'yes' === $explicit);
            }

            $publishedDate = $item->get_date('Y-m-d');
            if ($publishedDate && (is_null($publication->getData('datePublished')) || '' === trim($publication->getData('datePublished')))) {
                $publication->setData('datePublished', $publishedDate);
            }

            $duration = $this->getItemTagValue($item, SimplePie::NAMESPACE_ITUNES, 'duration');
            if ($duration && (is_null($publication->getData('duration')) || '' === $publication->getData('duration'))) {
                $durationStr = '';
                if (preg_match('/^\\d*:?\\d{1,2}:\\d{2}$/i', $duration)) {
                    $durationStr = $duration;
                } elseif (preg_match('/^\\d+$/i', $duration)) {
                    $durationStr = gmdate('H:i:s', (int) $duration);
                }
                // remove leading zeros and `:` to shorten duration string
                $publication->setData('duration', preg_replace('/^[0:]*/i', '', $durationStr));
            }

            $title = $item->get_title();
            if ($title && (is_null($publication->getData('title', $this->locale)) || '' === $publication->getData('title', $this->locale))) {
                $publication->setData('title', html_entity_decode($title), $this->locale);
            }

            $subtitle = $this->getItemTagValue($item, SimplePie::NAMESPACE_ITUNES, 'subtitle');
            if ($subtitle && (is_null($publication->getData('subtitle', $this->locale)) || '' === $publication->getData('subtitle', $this->locale))) {
                $publication->setData('subtitle', html_entity_decode($subtitle), $this->locale);
            }

            $description = $item->get_content();
            if ($description && (is_null($publication->getData('abstract', $this->locale)) || '' === $publication->getData('abstract', $this->locale))) {
                $publication->setData('abstract', html_entity_decode($description), $this->locale);
            }

            $keywords = [];
            $keywordsString = trim($this->getItemTagValue($item, SimplePie::NAMESPACE_ITUNES, 'keywords') ?? '');
            if ('' !== $keywordsString) {
                foreach (explode(',', $keywordsString) as $keyword) {
                    $keywords[]=trim($keyword);
                }
            }
            $keywords = array_unique(array_merge($this->podcastKeywords, $keywords));
            if (count($keywords) > 0 && (is_null($publication->getData('keywords', $this->locale)) || empty($publication->getData('keywords', $this->locale)))) {
                $keywords = array_map(function ($keyword) {
                    return ['name' => $keyword];
                }, $keywords);
                $publication->setData('keywords', $keywords, $this->locale);
            }

            $podcastEpisodeSource = $item->get_link();
            if ($podcastEpisodeSource && (is_null($publication->getData('source', $this->locale)) || '' === $publication->getData('source', $this->locale))) {
                $publication->setData('source', $podcastEpisodeSource, $this->locale);
            }

            // can't get coverage from rss feed
            // can't get agencies from rss feed

            if (is_null($publication->getData('type', $this->locale)) || '' === $publication->getData('type', $this->locale)) {
                $publication->setData('type', 'Sound', $this->locale);
            }

            $imageTags = $item->get_item_tags(SimplePie::NAMESPACE_ITUNES, 'image') ?? [];
            if ($imageTags && (is_null($publication->getData('coverImage', $this->locale)) || empty($publication->getData('coverImage', $this->locale)))) {
                // get the first valid image and add it as coverImage
                foreach ($imageTags as $imageTag) {
                    if ($imageTag && array_key_exists('attribs', $imageTag) && array_key_exists('', $imageTag['attribs']) && array_key_exists('href', $imageTag['attribs'][''])) {
                        $imageUrl = $imageTag['attribs']['']['href'];

                        $originalFileName = basename(parse_url($imageUrl, PHP_URL_PATH));
                        $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);

                        $imageTempPath = $this->tempFiles[$imageUrl] ?? $this->downloadFileToTemp($imageUrl);
                        if (!is_null($imageTempPath)) {
                            [$width, $height] = getimagesize($imageTempPath);
                            $publicFilename = "submission_{$submission->getId()}_{$publication->getId()}_coverImage_{$this->locale}.$extension";
                            $this->publicFileManager->copyContextFile($this->journal->getId(), $imageTempPath, $publicFilename);
                            echo "Saving publication coverImage `{$publicFilename}`\n";
                            $publication->setData('coverImage', [
                                'name' => $originalFileName,
                                'uploadName' => $publicFilename,
                                'width' => $width,
                                'height' => $height,
                                'dateUploaded' => Core::getCurrentDate(),
                                'altText' => '',
                            ], $this->locale);
                            // only get the first image
                            break;
                        }
                    }
                }
            }

            // only download resources if there are no existing
            $existingGalley = Repo::galley()->getCollector()
                ->filterByContextIds([$this->journal->getId()])
                ->filterByPublicationIds([$publication->getId()])
                ->getMany()
                ->first();
            if (is_null($existingGalley)) {
                $remoteResources = [];
                // NOTE: enclosure can be any file type (ex: audio, image) but there should be at least one audio file for a podcast
                foreach ($item->get_enclosures() as $enclosure) {
                    if ($enclosure && $enclosure->get_link()) {
                        $remoteResources[] = [
                            'url' => $enclosure->get_link(),
                            'mimeType' => $enclosure->get_type(),
                            // 'image', 'audio', 'video', 'document', 'executable'
                            'medium' => $enclosure->get_medium(),
                        ];
                    }
                }
                // there might be transcripts
                $transcriptUrls = $item->get_item_tags(self::NS_PODCAST, 'transcript') ?? [];
                foreach ($transcriptUrls as $transcriptUrlData) {
                    if ($transcriptUrlData && array_key_exists('attribs', $transcriptUrlData) && array_key_exists('', $transcriptUrlData['attribs']) && array_key_exists('href', $transcriptUrlData['attribs']['']) && $transcriptUrlData['attribs']['']['url']) {
                        $remoteResources[] = [
                            'url' => $transcriptUrlData['attribs']['']['url'],
                            'mimeType' => $transcriptUrlData['attribs']['']['type'],
                            'medium' => 'document',
                        ];
                    }
                }

                foreach ($remoteResources as $remoteResource) {
                    $fileUrl = $remoteResource['url'];
                    $fileMimetype = $remoteResource['mimeType'] ?? '';
                    $fileMedium = $remoteResource['medium'] ?? '';

                    $originalFileName = basename(parse_url($fileUrl, PHP_URL_PATH));
                    $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);

                    $fileTempPath = $this->tempFiles[$fileUrl] ?? $this->downloadFileToTemp($fileUrl);
                    if (!is_null($fileTempPath)) {
                        $submissionFilename = uniqid() . '.' . $extension;
                        $submissionDir = Repo::submissionFile()->getSubmissionDir($this->journal->getId(), $submission->getId());
                        echo "Saving galley file to `{$submissionDir}/{$submissionFilename}`\n";
                        $fileId = Services::get('file')->add($fileTempPath, "{$submissionDir}/{$submissionFilename}");

                        // figure out the genre
                        $genreId = $this->otherGenreId;
                        if ($this->submissionGenreId && ($fileMedium === 'audio' || str_starts_with($fileMimetype, 'audio/'))) {
                            $genreId = $this->submissionGenreId;
                        } elseif ($this->transcriptGenreId && ($fileMedium === 'document' || in_array($fileMimetype, ['text/plain', 'text/html', 'text/vtt', 'application/json', 'application/x-subrip']))) {
                            $genreId = $this->transcriptGenreId;
                        } elseif ($this->imageGenreId && ($fileMedium === 'image' || str_starts_with($fileMimetype, 'image/'))) {
                            $genreId = $this->imageGenreId;
                        }

                        $submissionFile = new SubmissionFile();
                        $submissionFile->setData('submissionId', $submission->getId());
                        $submissionFile->setData('fileId', $fileId);
                        $submissionFile->setData('genreId', $genreId);
                        $submissionFile->setData('name', $originalFileName, $this->locale);
                        $submissionFile->setData('fileStage', SubmissionFile::SUBMISSION_FILE_PROOF);
                        $submissionFile->setData('createdAt', Core::getCurrentDate());
                        $submissionFile->setData('updatedAt', Core::getCurrentDate());
                        $submissionFile->setData('uploaderUserId', $this->adminUser->getId());

                        $submissionFileId = Repo::submissionFile()->dao->insert($submissionFile);
                        $submissionFile = REPO::submissionFile()->get($submissionFileId);

                        $galley = new Galley();
                        $galley->setData('publicationId', $publication->getId());
                        $galley->setData('submissionFileId', $submissionFile->getId());
                        $galley->setLabel('Audio');
                        $galley->setLocale($this->locale);
                        $galleyId = Repo::galley()->dao->insert($galley);
                        $galley = REPO::galley()->get($galleyId);

                        $submissionFile->setData('assocType', Application::ASSOC_TYPE_GALLEY);
                        $submissionFile->setData('assocId', $galley->getId());
                        Repo::submissionFile()->dao->update($submissionFile);
                        $submissionFile = REPO::submissionFile()->get($submissionFile->getId());
                    }
                }
            }

            REPO::publication()->dao->update($publication);
            $publication = REPO::publication()->get($publication->getId());
        }
    }

    protected function addContributorRoles() : void {
        $existingAuthorUserGroups = UserGroup::withRoleIds([Role::ROLE_ID_AUTHOR])
            ->withContextIds([$this->journal->getId()])
            ->get();
        $existingAbbrevs = [];
        foreach ($existingAuthorUserGroups as $existingAuthorUserGroup) {
            $existingAbbrevs[] = $existingAuthorUserGroup->abbrev['en'] ?? null;
        }

        $contributorRoles = [
            [
                'name' => ['en' => __('plugins.generic.podcast.groups.hst.name', [], 'en')],
                'abbrev' => ['en' => __('plugins.generic.podcast.groups.hst', [], 'en')],
            ],
            [
                'name' => ['en' => __('plugins.generic.podcast.groups.pro.name', [], 'en')],
                'abbrev' => ['en' => __('plugins.generic.podcast.groups.pro', [], 'en')],
            ],
            [
                'name' => ['en' => __('plugins.generic.podcast.groups.ivr.name', [], 'en')],
                'abbrev' => ['en' => __('plugins.generic.podcast.groups.ivr', [], 'en')],
            ],
            [
                'name' => ['en' => __('plugins.generic.podcast.groups.ive.name', [], 'en')],
                'abbrev' => ['en' => __('plugins.generic.podcast.groups.ive', [], 'en')],
            ],
            [
                'name' => ['en' => __('plugins.generic.podcast.groups.aup.name', [], 'en')],
                'abbrev' => ['en' => __('plugins.generic.podcast.groups.aup', [], 'en')],
            ],
            [
                'name' => ['en' => __('plugins.generic.podcast.groups.ctb.name', [], 'en')],
                'abbrev' => ['en' => __('plugins.generic.podcast.groups.ctb', [], 'en')],
            ],
        ];

        foreach ($contributorRoles as $contributorRole) {
            if (in_array($contributorRole['abbrev']['en'], $existingAbbrevs)) {
                continue;
            }

            // Create missing contributor role
            $authorUserGroup = new UserGroup([
                'roleId' => Role::ROLE_ID_AUTHOR,
                'contextId' => $this->journal->getId(),
                'isDefault' => true,
                'showTitle' => true,
                'permitSelfRegistration' => false,
                'permitMetadataEdit' => false,
                'permitSettings' => false,
                'masthead' => false,
                'name' => $contributorRole['name'],
                'abbrev' => $contributorRole['abbrev'],
            ]);
            $authorUserGroup->fill($contributorRole);
            $authorUserGroup->save();
        }
    }

    protected function fixPermissions(string $path, string $user, string $group): bool {
        if (!file_exists($path)) {
            return false;
        }
        if (!chown($path, $user) || !chgrp($path, $group)) {
            return false;
        }
        if (is_dir($path)) {
            $items = scandir($path);

            foreach ($items as $item) {
                if ($item != '.' && $item != '..') {
                    $itemPath = $path . DIRECTORY_SEPARATOR . $item;
                    if (!$this->fixPermissions($itemPath, $user, $group)) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    protected function cleanup() : void {
        // cleanup all the temp files
        foreach($this->tempFiles as $url => $tempFilePath) {
            $this->temporaryFileManager->deleteByPath($tempFilePath);
        }
        // fix owner/group permission for apache
        $contextPublicPath = $this->publicFileManager->getContextFilesPath($this->journal->getId());
        $this->fixPermissions($contextPublicPath, 'www-data', 'www-data');
        $contextPrivatePath = Config::getVar('files', 'files_dir') . '/journals/' . $this->journal->getId();
        $this->fixPermissions($contextPrivatePath, 'www-data', 'www-data') . '.';
    }
}

// For backwards compatibility -- expect this to be removed approx. OJS/OMP/OPS 3.6
if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\generic\podcast\PodcastImportPlugin', '\PodcastImportPlugin');
}