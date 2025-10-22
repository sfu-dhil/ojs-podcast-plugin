<?php

namespace APP\plugins\generic\podcast;

use APP\core\Application;
use PKP\plugins\Hook;
use PKP\plugins\PluginRegistry;
use PKP\plugins\GenericPlugin;
use PKP\plugins\ThemePlugin;
use APP\template\TemplateManager;
use APP\plugins\generic\podcast\classes\DownloadHandler;
use APP\plugins\generic\podcast\classes\GalleyAudioViewer;
use APP\plugins\generic\podcast\classes\AuthorHandler;
use APP\plugins\generic\podcast\classes\JournalHandler;
use APP\plugins\generic\podcast\classes\IssueHandler;
use APP\plugins\generic\podcast\classes\SubmissionPublicationHandler;
use APP\plugins\generic\podcast\classes\GatewayHandler;
use APP\plugins\generic\podcast\PodcastImportPlugin;
use APP\plugins\generic\podcast\PodcastFeedGatewayPlugin;
use APP\plugins\generic\podcast\PodcastBlockPlugin;

class PodcastPlugin extends GenericPlugin
{
    public function register($category, $path, $mainContextId = null): bool
    {
        $success = parent::register($category, $path);
        if ($success && $this->getEnabled()) {
            $request = Application::get()->getRequest();
            $templateMgr = TemplateManager::getManager($request);
            $activeTheme = $this->getActiveTheme($mainContextId);
            $pluginAssetPath = $request->getBaseUrl() . '/' . $this->getPluginPath() . '/assets';

            // material additional css overrides
            if (!is_null($activeTheme) && 'materialthemeplugin' === $activeTheme->getName()) {
                $templateMgr->addStyleSheet('material_override', "$pluginAssetPath/css/material_override.css");
            }

            // disable the webFeedPlugin since the PodcastPlugin is generating a Podcast rss feed
            $webFeedPlugin = PluginRegistry::getPlugin('generic', 'webfeedplugin');
            if (!is_null($webFeedPlugin) && $webFeedPlugin->getEnabled()) {
                $webFeedPlugin->setEnabled(false);
            }

            // gateway handlers
            $podcastFeedGatewayPlugin = new PodcastFeedGatewayPlugin(podcastPlugin: $this);
            PluginRegistry::register('gateways', $podcastFeedGatewayPlugin, $this->getPluginPath());
            $podcastBlockPlugin = new PodcastBlockPlugin(podcastPlugin: $this, activeTheme: $activeTheme);
            PluginRegistry::register('blocks', $podcastBlockPlugin, $this->getPluginPath());
            $gatewayHandler = new GatewayHandler($this, $podcastFeedGatewayPlugin);
            Hook::add('TemplateManager::display', [$gatewayHandler, 'addGatewayLinks']);

            // override other OJS templates (needed for submission review template)
            Hook::add('TemplateResource::getFilename', [$this, '_overridePluginTemplates']);

            // Audio player for galley view
            $galleyAudioViewer = new GalleyAudioViewer($this);
            Hook::add('ArticleHandler::view::galley', [$galleyAudioViewer, 'viewSubmissionGalley'], Hook::SEQUENCE_LAST);
            Hook::add('IssueHandler::view::galley', [$galleyAudioViewer, 'viewIssueGalley'], Hook::SEQUENCE_LAST);

            // Add Podcast level metadata to Journal (context)
            $journalHandler = new JournalHandler($this);
            Hook::add('Schema::get::context', [$journalHandler, 'contextSchema']);
            Hook::add('Form::config::before', [$journalHandler, 'contextForm']);

            // Add Season level metadata to Issue
            $issueHandler = new IssueHandler($this);
            Hook::add('Schema::get::issue', [$issueHandler, 'issueSchema']);
            // Hook::add('Form::config::before', [$issueHandler, 'issueForm']);

            // Add Episode level metadata to publication
            $submissionPublicationHandler = new SubmissionPublicationHandler($this);
            Hook::add("Schema::get::submission", [$submissionPublicationHandler, 'submissionSchema']);
            Hook::add('Schema::get::publication', [$submissionPublicationHandler, 'publicationSchema']);
            Hook::add('Publication::validate', [$submissionPublicationHandler, 'publicationValidate']);
            // Hook::add('Form::config::before', [$submissionPublicationHandler, 'publicationForm']);
            Hook::add('Form::config::before', [$submissionPublicationHandler, 'publicationMetadataForm']);
            Hook::add('Templates::Article::Details', [$submissionPublicationHandler, 'articleAdditionalDetails']);

            // Podcast Contributors/Authors
            $authorHandler = new AuthorHandler(plugin: $this);
            Hook::add('Schema::get::author', [$authorHandler, 'authorSchema']);
            Hook::add('Form::config::before', [$authorHandler, 'contributorForm']);

            // File downloads
            $downloadHandler = new DownloadHandler($this);
            Hook::add('File::download', [$downloadHandler, 'xSendFileDownload']);
        }
        // Register the podcast rss import plugin (needs to be outside of PodcastPlugin getEnabled block to work for CLI)
        $podcastImportPlugin = new PodcastImportPlugin(podcastPlugin: $this);
        PluginRegistry::register('importexport', $podcastImportPlugin, $this->getPluginPath());
        return $success;
    }

    public function getContextSpecificPluginSettingsFile()
    {
        return $this->getPluginPath() . '/settings.xml';
    }

    public function getInstallSitePluginSettingsFile()
    {
        return $this->getPluginPath() . '/settings.xml';
    }

    public function getDisplayName(): string
    {
        return __('plugins.generic.podcast.displayName');
    }

    public function getDescription(): string
    {
        return __('plugins.generic.podcast.description');
    }

    public function getActiveTheme($mainContextId = null): ?ThemePlugin
    {
        $contextThemePlugins = PluginRegistry::loadCategory('themes', true, $mainContextId);
        foreach ($contextThemePlugins as $theme) {
            if ($theme->isActive()) {
                return $theme;
            }
        }
        return null;
    }
}

// For backwards compatibility -- expect this to be removed approx. OJS/OMP/OPS 3.6
if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\generic\podcast\PodcastPlugin', '\PodcastPlugin');
}