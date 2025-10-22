<?php

namespace APP\plugins\generic\podcast\classes;

use APP\plugins\generic\podcast\PodcastPlugin;
use APP\core\Application;
use PKP\plugins\Hook;
use PKP\context\Context;
use APP\facades\Repo;
use PKP\components\forms\FieldText;
use PKP\components\forms\FieldSelect;
use PKP\components\forms\FormComponent;
use PKP\components\forms\publication\TitleAbstractForm;
use PKP\components\forms\publication\PKPMetadataForm;
use PKP\components\forms\submission\ForTheEditors;
use APP\plugins\generic\podcast\classes\enums\EpisodeType;

class SubmissionPublicationHandler
{
    public function __construct(
        public PodcastPlugin $plugin
    ) {}

    public function submissionSchema(string $hookName, array $args): bool
    {
        $schema = &$args[0];

        $schema->properties->importRssGuid = (object) [
            'type' => 'string',
            'multilingual' => false,
            'apiSummary' => true,
            'validation' => ['nullable'],
        ];

        return HOOK::CONTINUE;
    }

    public function publicationSchema(string $hookName, array $args): bool
    {
        $schema = &$args[0];

        $schema->properties->explicit = (object) [
            'type' => 'boolean',
            'multilingual' => false,
            'apiSummary' => true,
            'validation' => ['nullable'],
            // 'validation' => ['required'],
        ];
        $schema->properties->episodeType = (object) [
            'type' => 'string',
            'multilingual' => false,
            'apiSummary' => true,
            'validation' => ['nullable'],
            // 'validation' => ['required'],
        ];
        $schema->properties->episodeNumber = (object) [
            'type' => 'string',
            'multilingual' => false,
            'apiSummary' => true,
            'validation' => ['nullable'],
            // 'validation' => ['required', 'Numeric'],
        ];
        $schema->properties->duration = (object) [
            'type' => 'string',
            'multilingual' => false,
            'apiSummary' => true,
            'validation' => ['nullable'],
            // 'validation' => ['required', 'regex:/^\d*:?[0-5]?\d:[0-5]\d$/'],
        ];

        return HOOK::CONTINUE;
    }

    public function publicationValidate(string $hookName, array $args): bool
    {
        $errors = &$args[0];
        $object = &$args[1];
        $props = &$args[2];
        $allowedLocales = &$args[3];
        $primaryLocale = &$args[4];

        // if (is_null($object)) {
        //     $submission = Repo::submission()->get($props['submissionId']);
        // } else {
        //     $publication = Repo::publication()->get($props['id']);
        //     $submission = Repo::submission()->get($publication->getData('submissionId'));
        // }

        // if ($props['explicit'] === null) {
        //     $errors['explicit'] = [__('validator.required')];
        // }
        // if ($props['episodeType'] === null || $props['episodeType'] === '') {
        //     $errors['episodeType'] = [__('validator.required')];
        // } elseif (EpisodeType::tryFrom($props['episodeType']) === null) {
        //     $errors['episodeType'] = [__('plugins.generic.podcast.validator.episodeType')];
        // }
        // if ($props['episodeNumber'] === null || $props['episodeNumber'] === '') {
        //     $errors['episodeNumber'] = [__('validator.required')];
        // } elseif (is_numeric($props['episodeNumber']) === false) {
        //     $errors['episodeNumber'] = [__('plugins.generic.podcast.validator.episodeNumber')];
        // }
        // if ($props['duration'] === null || $props['duration'] === '') {
        //     $errors['duration'] = [__('validator.required')];
        // } elseif (@preg_match('^\d*:?[0-5]?\d:[0-5]\d$', $props['duration']) === false) {
        //     $errors['duration'] = [__('plugins.generic.podcast.validator.duration')];
        // }

        return HOOK::CONTINUE;
    }

    public function publicationForm(string $hookName, FormComponent $form): bool
    {
        // Only modify the masthead form
        if ($form->id !== TitleAbstractForm::FORM_TITLE_ABSTRACT) {
            return HOOK::CONTINUE;
        }

        // Don't do anything at the site-wide level
        $context = Application::get()->getRequest()->getContext();
        $publication = $form->publication;

        if (!$context || !$publication) {
            return HOOK::CONTINUE;
        }

        // Add a field to the form
        $form->addField(new FieldSelect('explicit', [
            'label' => __('plugins.generic.podcast.explicit'),
            'groupId' => 'default',
            'pageId' => 'default',
            'options' => [
                ['value' => false, 'label' => __('common.no')],
                ['value' => true, 'label' => __('common.yes')],
            ],
            'default' => false,
            'value' => $publication->getData('explicit'),
            // 'isRequired' => true,
        ]), [ 'FIELD_POSITION_AFTER', 'subjects' ]);
        $form->addField(new FieldSelect('episodeType', [
            'label' => __('plugins.generic.podcast.episodeType'),
            'groupId' => 'default',
            'pageId' => 'default',
            'options' => [
                ['value' => EpisodeType::FULL, 'label' => EpisodeType::FULL->label()],
                ['value' => EpisodeType::BONUS, 'label' => EpisodeType::BONUS->label()],
                ['value' => EpisodeType::TRAILER, 'label' => EpisodeType::TRAILER->label()],
            ],
            'default' => EpisodeType::FULL,
            'value' => $publication->getData('episodeType'),
            // 'isRequired' => true,
        ]), [ 'FIELD_POSITION_AFTER', 'explicit' ]);
        $form->addField(new FieldText('episodeNumber', [
            'label' => __('plugins.generic.podcast.episodeNumber'),
            'groupId' => 'default',
            'pageId' => 'default',
            'default' => 1,
            'value' => $publication->getData('episodeNumber'),
            'description' => __('plugins.generic.podcast.episodeNumber.description'),
            // 'isRequired' => true,
        ]), [ 'FIELD_POSITION_AFTER', 'episodeType' ]);
        $form->addField(new FieldText('duration', [
            'label' => __('plugins.generic.podcast.duration'),
            'groupId' => 'default',
            'pageId' => 'default',
            'default' => '0:00:00',
            'value' => $publication->getData('duration'),
            'description' => __('plugins.generic.podcast.duration.description'),
            // 'isRequired' => true,
        ]), [ 'FIELD_POSITION_AFTER', 'episodeNumber' ]);

        return HOOK::CONTINUE;
    }

    public function publicationMetadataForm(string $hookName, FormComponent $form): bool
    {
        // Only modify the masthead form
        if (!in_array($form->id, ['forTheEditors', PKPMetadataForm::FORM_METADATA])) {
            return HOOK::CONTINUE;
        }

        // Don't do anything at the site-wide level
        $context = $form->context;
        $publication = $form->publication;

        if (!$context || !$publication) {
            return HOOK::CONTINUE;
        }

        // Add a field to the form
        $form->addField(new FieldSelect('explicit', [
            'label' => __('plugins.generic.podcast.explicit'),
            'groupId' => 'default',
            'options' => [
                ['value' => false, 'label' => __('common.no')],
                ['value' => true, 'label' => __('common.yes')],
            ],
            'default' => false,
            'value' => $publication->getData('explicit'),
            'isRequired' => true,
        ]), [ 'before', 'subjects' ]);
        $form->addField(new FieldSelect('episodeType', [
            'label' => __('plugins.generic.podcast.episodeType'),
            'groupId' => 'default',
            'options' => [
                ['value' => EpisodeType::FULL, 'label' => EpisodeType::FULL->label()],
                ['value' => EpisodeType::BONUS, 'label' => EpisodeType::BONUS->label()],
                ['value' => EpisodeType::TRAILER, 'label' => EpisodeType::TRAILER->label()],
            ],
            'default' => EpisodeType::FULL,
            'value' => $publication->getData('episodeType'),
            'isRequired' => true,
        ]), [ 'after', 'explicit' ]);
        $form->addField(new FieldText('episodeNumber', [
            'label' => __('plugins.generic.podcast.episodeNumber'),
            'groupId' => 'default',
            'default' => '1',
            'value' => $publication->getData('episodeNumber'),
            'description' => __('plugins.generic.podcast.episodeNumber.description'),
            'isRequired' => true,
        ]), [ 'after', 'episodeType' ]);
        $form->addField(new FieldText('duration', [
            'label' => __('plugins.generic.podcast.duration'),
            'groupId' => 'default',
            'default' => '0:00:00',
            'value' => $publication->getData('duration'),
            'description' => __('plugins.generic.podcast.duration.description'),
            'isRequired' => true,
        ]), [ 'after', 'episodeNumber' ]);

        return HOOK::CONTINUE;
    }

    public function articleAdditionalDetails(string $hookName, array $args): bool
    {
        $smarty = &$args[1];
        $output = &$args[2];

        $activeTheme = $smarty->getTemplateVars('activeTheme');
        if ('materialthemeplugin' === $activeTheme->getName()) {
            $output .= $smarty->fetch($this->plugin->getTemplateResource('material_episode_details.tpl'));
        } else {
            $output .= $smarty->fetch($this->plugin->getTemplateResource('episode_details.tpl'));
        }
        return Hook::CONTINUE;
    }

    protected function metadataEnabled(string $setting): bool
    {
        $context = Application::get()->getRequest()->getContext();
        return (bool) $context->getData($setting);
    }
}
