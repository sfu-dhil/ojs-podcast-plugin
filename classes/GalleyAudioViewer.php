<?php

namespace APP\plugins\generic\podcast\classes;

use APP\plugins\generic\podcast\PodcastPlugin;
use APP\core\Application;
use APP\template\TemplateManager;
use PKP\plugins\Hook;

class GalleyAudioViewer
{
    public function __construct(
        public PodcastPlugin $plugin
    ) {}

    public function viewSubmissionGalley(string $hookName, array $args): bool
    {
        $request = &$args[0];
        $application = Application::get();

        $issue = &$args[1];
        $galley = &$args[2];
        $submission = &$args[3];
        $submissionNoun = 'article';


        if ($galley && str_starts_with($galley->getFileType(), 'audio/')) {
            $galleyPublication = null;
            foreach ($submission->getData('publications') as $publication) {
                if ($publication->getId() === $galley->getData('publicationId')) {
                    $galleyPublication = $publication;
                    break;
                }
            }
            $templateMgr = TemplateManager::getManager($request);

            if ($galleyPublication) {
                $title = $galleyPublication->getLocalizedTitle(null, 'html');
            }

            $audioUrl = $request->url(
                null,
                $submissionNoun,
                'download',
                [$submission->getBestId(), $galley->getBestGalleyId(), $galley->getFile()->getId()],
                ['inline' => 1],
            );

            $parentUrl = $request->url(null, $submissionNoun, 'view', [$submission->getBestId()]);

            $galleyTitle = __('submission.representationOfTitle', [
                'representation' => $galley->getLabel(),
                'title' => $galleyPublication->getLocalizedFullTitle(),
            ]);

            $datePublished = __('submission.outdatedVersion', [
                'datePublished' => $galleyPublication->getData('datePublished'),
                'urlRecentVersion' => $parentUrl,
            ]);

            $templateMgr->assign([
                'displayTemplateResource' => $this->plugin->getTemplateResource('display.tpl'),
                'pluginUrl' => $request->getBaseUrl() . '/' . $this->plugin->getPluginPath(),
                'galleyFile' => $galley->getFile(),
                'issue' => $issue,
                'submission' => $submission,
                'submissionNoun' => $submissionNoun,
                'bestId' => $galleyPublication->getData('urlPath') ?? $submission->getId(),
                'galley' => $galley,
                'currentVersionString' => $application->getCurrentVersion()->getVersionString(false),
                'isLatestPublication' => $submission->getData('currentPublicationId') === $galley->getData('publicationId'),
                'galleyPublication' => $galleyPublication,
                'title' => $title,
                'audioUrl' => $audioUrl,
                'audioType' => $galley->getFileType(),
                'parentUrl' => $parentUrl,
                'galleyTitle' => $galleyTitle,
                'datePublished' => $datePublished,
                'isTitleHtml' => true,
            ]);

            $activeTheme = $templateMgr->getTemplateVars('activeTheme');
            if ('materialthemeplugin' === $activeTheme->getName()) {
                $templateMgr->addStyleSheet('podcast_plyr_css', "{$request->getBaseUrl()}/{$this->plugin->getPluginPath()}//assets/css/podcast_plyr_material.css");
                $templateMgr->addJavaScript('podcast_plyr_js', "{$request->getBaseUrl()}/{$this->plugin->getPluginPath()}//assets/js/podcast_plyr.js");
                $templateMgr->display($this->plugin->getTemplateResource('material_audio_display.tpl'));
            } else {
                $templateMgr->addStyleSheet('podcast_plyr_css', "{$request->getBaseUrl()}/{$this->plugin->getPluginPath()}//assets/css/podcast_plyr.css");
                $templateMgr->addJavaScript('podcast_plyr_js', "{$request->getBaseUrl()}/{$this->plugin->getPluginPath()}//assets/js/podcast_plyr.js");
                $templateMgr->display($this->plugin->getTemplateResource('audio_display.tpl'));
            }
            return HOOK::ABORT;
        }

        return HOOK::CONTINUE;
    }

    public function viewIssueGalley(string $hookName, array $args): bool
    {
        $request = &$args[0];
        $issue = &$args[1];
        $galley = &$args[2];

        if ($galley && str_starts_with($galley->getFileType(), 'audio/')) {
            $templateMgr = TemplateManager::getManager($request);
            $application = Application::get();

            $audioUrl = $request->url(
                null,
                'issue',
                'download',
                [$issue->getBestIssueId(), $galley->getBestGalleyId()],
                ['inline' => 1],
            );

            $parentUrl = $request->url(null, 'issue', 'view', [$issue->getBestIssueId()]);

            $galleyTitle = __('submission.representationOfTitle', [
                'representation' => $galley->getLabel(),
                'title' => $issue->getIssueIdentification(),
            ]);

            $datePublished = __('submission.outdatedVersion', [
                'datePublished' => $issue->getData('datePublished'),
                'urlRecentVersion' => $parentUrl,
            ]);

            $title = $issue->getIssueIdentification();

            $templateMgr->assign([
                'displayTemplateResource' => $this->plugin->getTemplateResource('display.tpl'),
                'pluginUrl' => $request->getBaseUrl() . '/' . $this->plugin->getPluginPath(),
                'galleyFile' => $galley->getFile(),
                'issue' => $issue,
                'galley' => $galley,
                'currentVersionString' => $application->getCurrentVersion()->getVersionString(false),
                'isLatestPublication' => true,
                'audioUrl' => $audioUrl,
                'audioType' => $galley->getFileType(),
                'parentUrl' => $parentUrl,
                'galleyTitle' => $galleyTitle,
                'datePublished' => $datePublished,
                'title' => $title,
                'isTitleHtml' => false,
            ]);

            $activeTheme = $templateMgr->getTemplateVars('activeTheme');
            if ('materialthemeplugin' === $activeTheme->getName()) {
                $templateMgr->addStyleSheet('podcast_plyr_css', "{$request->getBaseUrl()}/{$this->plugin->getPluginPath()}//assets/css/podcast_plyr_material.css");
                $templateMgr->addJavaScript('podcast_plyr_js', "{$request->getBaseUrl()}/{$this->plugin->getPluginPath()}//assets/js/podcast_plyr.js");
                $templateMgr->display($this->plugin->getTemplateResource('material_audio_display.tpl'));
            } else {
                $templateMgr->addStyleSheet('podcast_plyr_css', "{$request->getBaseUrl()}/{$this->plugin->getPluginPath()}//assets/css/podcast_plyr.css");
                $templateMgr->addJavaScript('podcast_plyr_js', "{$request->getBaseUrl()}/{$this->plugin->getPluginPath()}//assets/js/podcast_plyr.js");
                $templateMgr->display($this->plugin->getTemplateResource('audio_display.tpl'));
            }
            return HOOK::ABORT;
        }

        return HOOK::CONTINUE;
    }
}
