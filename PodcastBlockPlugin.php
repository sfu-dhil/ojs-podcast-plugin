<?php

namespace APP\plugins\generic\podcast;

use PKP\plugins\BlockPlugin;
use PKP\plugins\ThemePlugin;

class PodcastBlockPlugin extends BlockPlugin
{
    public function __construct(
        protected PodcastPlugin $podcastPlugin,
        protected ?ThemePlugin $activeTheme,
    ) {
        parent::__construct();
    }

    public function getName(): string
    {
        return 'PodcastBlockPlugin';
    }

    public function getHideManagement(): bool
    {
        return true;
    }

    public function getDisplayName(): string
    {
        return __('plugins.generic.podcast.displayName');
    }

    public function getDescription(): string
    {
        return __('plugins.generic.podcast.description');
    }

    public function getPluginPath(): string
    {
        return $this->podcastPlugin->getPluginPath();
    }

    public function getBlockTemplateFilename(): string
    {
        return !is_null($this->activeTheme) && 'materialthemeplugin' === $this->activeTheme->getName() ? 'material_block.tpl' : 'block.tpl';
    }

    public function getTemplatePath($inCore = false): string
    {
        return "{$this->podcastPlugin->getTemplatePath($inCore)}/templates";
    }
}

// For backwards compatibility -- expect this to be removed approx. OJS/OMP/OPS 3.6
if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\generic\podcast\PodcastBlockPlugin', '\PodcastBlockPlugin');
}