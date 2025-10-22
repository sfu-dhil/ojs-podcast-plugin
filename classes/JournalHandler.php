<?php

namespace APP\plugins\generic\podcast\classes;

use APP\plugins\generic\podcast\PodcastPlugin;
use APP\core\Application;
use PKP\plugins\Hook;
use PKP\components\forms\FieldText;
use PKP\components\forms\FieldSelect;
use PKP\components\forms\FieldOptions;
use PKP\components\forms\FormComponent;
use APP\components\forms\context\MastheadForm;
use APP\plugins\generic\podcast\classes\enums\ItunesCategory;
use APP\plugins\generic\podcast\classes\enums\ItunesSubCategory;

class JournalHandler
{
    public function __construct(
        public PodcastPlugin $plugin
    ) {}

    public function contextSchema(string $hookName, array $args): bool
    {
        $schema = &$args[0];

        $itunesCategoriesOptions = [];
        foreach(ItunesCategory::cases() as $itunesCategory) {
            $itunesCategoriesOptions[]=$itunesCategory->value;
            foreach($itunesCategory->subcategories() as $itunesSubcategory) {
                $itunesCategoriesOptions[]=$itunesSubcategory->value;
            }
        }
        $itunesCategoriesOptionsStr = join(',', $itunesCategoriesOptions);

        $schema->properties->rssFeed = (object) [
            'type' => 'string',
            'multilingual' => false,
            'apiSummary' => true,
            'validation' => ['nullable'],
        ];
        $schema->properties->rssGuid = (object) [
            'type' => 'string',
            'multilingual' => false,
            'apiSummary' => true,
            'validation' => ['nullable'],
        ];
        $schema->properties->explicit = (object) [
            'type' => 'boolean',
            'multilingual' => false,
            'apiSummary' => true,
            'validation' => ['nullable'],
        ];
        $schema->properties->itunesCategories = (object) [
            'type' => 'array',
            'items' => (object) [
                'type' => 'string',
                'multilingual' => false,
                'apiSummary' => true,
                'validation' => [ "in:{$itunesCategoriesOptionsStr}"],
            ],
            'multilingual' => false,
            'apiSummary' => true,
            'validation' => ['nullable'],
        ];
        $schema->properties->podcastSource = (object) [
            'type' => 'string',
            'multilingual' => false,
            'apiSummary' => true,
            'validation' => ['nullable'],
        ];

        return HOOK::CONTINUE;
    }

    public function contextForm(string $hookName, FormComponent $form): bool
    {
        // Only modify the masthead form
        if ($form->id !== MastheadForm::FORM_MASTHEAD) {
            return HOOK::CONTINUE;
        }

        // Don't do anything at the site-wide level
        $context = Application::get()->getRequest()->getContext();
        if (!$context) {
            return HOOK::CONTINUE;
        }

        $itunesCategoriesOptions = [];
        foreach(ItunesCategory::cases() as $itunesCategory) {
            $itunesCategoriesOptions[]=[ 'value' => $itunesCategory->value, 'label' => $itunesCategory->label() ];
            foreach($itunesCategory->subcategories() as $itunesSubcategory) {
                $itunesCategoriesOptions[]=[ 'value' => $itunesSubcategory->value, 'label' => $itunesCategory->label(). ' - ' . $itunesSubcategory->label()];
            }
        }

        // Add a field to the form
        $form->addGroup(
            [ 'id' => 'podcast', 'label' => 'Podcast Details', ],
            [ 'FIELD_POSITION_AFTER', 'identity' ],
        );
        $form->addField(new FieldText('rssFeed', [
            'label' => __('plugins.generic.podcast.rssFeed'),
            'groupId' => 'podcast',
            'value' => $context->getData('rssFeed'),
        ]));
        $form->addField(new FieldText('rssGuid', [
            'label' => 'RSS GUID',
            'groupId' => 'podcast',
            'value' => $context->getData('rssGuid') ?? Application::get()->getUUID(),
        ]));
        $form->addField(new FieldSelect('explicit', [
            'label' => __('plugins.generic.podcast.explicit'),
            'groupId' => 'podcast',
            'options' => [
                ['value' => false, 'label' => __('common.no')],
                ['value' => true, 'label' => __('common.yes')],
            ],
            'default' => false,
            'value' => $context->getData('explicit'),
        ]));
        $form->addField(new FieldText('podcastSource', [
            'label' => __('plugins.generic.podcast.podcastSource'),
            'groupId' => 'podcast',
            'value' => $context->getData('podcastSource'),
        ]));
        $form->addField(new FieldOptions('itunesCategories', [
            'label' => __('plugins.generic.podcast.itunesCategories'),
            'groupId' => 'podcast',
            'value' => $context->getData('itunesCategories') ?? [],
            'options' => $itunesCategoriesOptions,
        ]));

        return HOOK::CONTINUE;
    }
}
