<?php

namespace APP\plugins\generic\podcast\classes;

use APP\plugins\generic\podcast\PodcastPlugin;
use PKP\plugins\Hook;

class IssueHandler
{
    public function __construct(
        public PodcastPlugin $plugin
    ) {}

    public function issueSchema(string $hookName, array $args): bool
    {
        $schema = &$args[0];
        return HOOK::CONTINUE;
    }
}
