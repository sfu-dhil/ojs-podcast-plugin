{**
 * templates/submission/review-details.tpl
 *
 * Copyright (c) 2014-2022 Simon Fraser University
 * Copyright (c) 2003-2022 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * The template in the submission wizard when reviewing the details step.
 *}
{foreach from=$locales item=$locale key=$localeKey}
    <div class="submissionWizard__reviewPanel">
        <div class="submissionWizard__reviewPanel__header">
            <h3 id="review{$step.id|escape}">
                {if count($locales) > 1}
                    {translate key="common.withParenthesis" item=$step.reviewName|escape inParenthesis=$locale}
                {else}
                    {$step.reviewName|escape}
                {/if}
            </h3>
            <pkp-button
                aria-describedby="review{$step.id|escape}"
                class="submissionWizard__reviewPanel__edit"
                @click="openStep('{$step.id|escape}')"
            >
                {translate key="common.edit"}
            </pkp-button>
        </div>
        <div
            class="
                submissionWizard__reviewPanel__body
                submissionWizard__reviewPanel__body--{$step.id|escape}
            "
        >
            {include file="/submission/review-publication-field.tpl" prop="title" inLocale=$localeKey name="{translate key="common.title"}" type="html"}
            {if in_array($currentContext->getData('keywords'), [$currentContext::METADATA_REQUEST, $currentContext::METADATA_REQUIRE])}
                {include file="/submission/review-publication-field.tpl" prop="keywords" inLocale=$localeKey name="{translate key="common.keywords"}" type="array" dataField="name"}
            {/if}
            {include file="/submission/review-publication-field.tpl" prop="abstract" inLocale=$localeKey name="{translate key="common.abstract"}" type="html"}
            {if in_array($currentContext->getData('plainLanguageSummary'), [$currentContext::METADATA_REQUEST, $currentContext::METADATA_REQUIRE])}
                {include file="/submission/review-publication-field.tpl" prop="plainLanguageSummary" inLocale=$localeKey name="{translate key="submission.plainLanguageSummary"}" type="html"}
            {/if}
            {if in_array($currentContext->getData('citations'), [$currentContext::METADATA_REQUEST, $currentContext::METADATA_REQUIRE])}
                {if $localeKey === $submission->getData('locale')}
                    <div class="submissionWizard__reviewPanel__item">
                        <template v-if="errors.citationsRaw">
                            <notification
                                v-for="(error, i) in errors.citationsRaw"
                                :key="i"
                                type="warning"
                            >
                                <icon icon="Error" class="h-5 w-5"></icon>
                                {{ error }}
                            </notification>
                        </template>
                        <h4 class="submissionWizard__reviewPanel__item__header">
                            {translate key="submission.citations"}
                        </h4>
                        <div class="submissionWizard__reviewPanel__item__value">
                            <template v-if="!publication.citationsRaw">
                                {translate key="common.noneProvided"}
                            </template>
                            <div
                                v-else
                                v-for="(citation, index) in publication.citationsRaw.trim().split(/(?:\r\n|\r|\n)/g).filter(c => c)"
                                :key="index"
                                class="submissionWizard__reviewPanel__citation"
                            >
                                {{ citation }}
                            </div>
                        </div>
                    </div>
                {/if}
            {/if}
            <div class="submissionWizard__reviewPanel__item">
                <template v-if="errors.explicit">
                    <notification v-for="(error, i) in errors.explicit" :key="i" type="warning">
                        <icon icon="Error" class="h-5 w-5"></icon>
                        {{ error }}
                    </notification>
                </template>
                <h4 class="submissionWizard__reviewPanel__item__header">
                    {translate key="plugins.generic.podcast.explicit"}
                </h4>
                <div class="submissionWizard__reviewPanel__item__value semantic-defaults">
                    <template v-if="publication.explicit">
                        {translate key="common.yes"}
                    </template>
                    <template v-else>
                        {translate key="common.no"}
                    </template>
                </div>
            </div>
            <div class="submissionWizard__reviewPanel__item">
                <template v-if="errors.episodeType">
                    <notification v-for="(error, i) in errors.episodeType" :key="i" type="warning">
                        <icon icon="Error" class="h-5 w-5"></icon>
                        {{ error }}
                    </notification>
                </template>
                <h4 class="submissionWizard__reviewPanel__item__header">
                    {translate key="plugins.generic.podcast.episodeType"}
                </h4>
                <div class="submissionWizard__reviewPanel__item__value semantic-defaults">
                    <template v-if="publication.episodeType == 'full'">
                        {translate key="plugins.generic.podcast.episodeType.full"}
                    </template>
                    <template v-else-if="publication.episodeType == 'bonus'">
                        {translate key="plugins.generic.podcast.episodeType.bonus"}
                    </template>
                    <template v-else-if="publication.episodeType == 'trailer'">
                        {translate key="plugins.generic.podcast.episodeType.trailer"}
                    </template>
                    <template v-else>
                        {translate key="common.noneProvided"}
                    </template>
                </div>
            </div>
            {include file="/submission/review-publication-field.tpl" prop="episodeNumber" name="{translate key="plugins.generic.podcast.episodeNumber"}" type="string"}
            {include file="/submission/review-publication-field.tpl" prop="duration" name="{translate key="plugins.generic.podcast.duration"}" type="string"}
            {call_hook name="Template::SubmissionWizard::Section::Review::Details" submission=$submission step=$step.id}
        </div>
    </div>
{/foreach}
