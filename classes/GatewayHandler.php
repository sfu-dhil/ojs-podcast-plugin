<?php

namespace APP\plugins\generic\podcast\classes;

use PKP\core\PKPPageRouter;
use APP\core\Application;
use APP\plugins\generic\podcast\PodcastPlugin;
use APP\plugins\generic\podcast\PodcastFeedGatewayPlugin;
use PKP\plugins\Hook;

class GatewayHandler
{
    public function __construct(
        public PodcastPlugin $plugin,
        public PodcastFeedGatewayPlugin $gatewayPlugin
    ) {}

    public function addGatewayLinks(string $hookName, array $args): bool
    {
        // Only page requests will be handled
        $request = Application::get()->getRequest();
        if (!($request->getRouter() instanceof PKPPageRouter)) {
            return Hook::CONTINUE;
        }

        $templateManager = $args[0];
        $context = $request->getContext();
        if (is_null($context)) {
            return Hook::CONTINUE;
        }

        $url = $request->url(null, 'gateway', 'plugin', [$this->gatewayPlugin->getName()]);
        $templateManager->addHeader(
            'PodcastFeedGatewayPlugin_rss_feed',
            '<link rel="alternate" type="application/rss+xml" href="'.$url.'">',
            ['contexts' => ['frontend']],
        );

        return Hook::CONTINUE;
    }
}