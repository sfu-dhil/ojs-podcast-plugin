<?php

namespace APP\plugins\generic\podcast\classes;

use APP\plugins\generic\podcast\PodcastPlugin;
use APP\core\Application;
use PKP\plugins\Hook;
use PKP\components\forms\FieldText;
use PKP\components\forms\FieldSelect;
use PKP\components\forms\FormComponent;
use PKP\components\forms\publication\ContributorForm;

class AuthorHandler
{
    public function __construct(
        public PodcastPlugin $plugin
    ) {}

    public function authorSchema(string $hookName, array $args): bool
    {
        $schema = &$args[0];

        // $schema->properties->email = (object) [
        //     'type' => 'string',
        //     'multilingual' => false,
        //     'apiSummary' => true,
        //     'validation' => ['nullable', 'email_or_localhost'],
        // ];

        return HOOK::CONTINUE;
    }

    public function contributorForm(string $hookName, FormComponent $form): bool
    {
        // Only modify the masthead form
        if ($form->id !== ContributorForm::FORM_CONTRIBUTOR) {
            return HOOK::CONTINUE;
        }

        // Add a field to the form
        $form->getField('email')->default = 'stub@localhost';
        $form->getField('country')->isRequired = false;

        return HOOK::CONTINUE;
    }
}
