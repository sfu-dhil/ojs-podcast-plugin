<div class="item podcast_episode">
    {if !empty($publication->getData('episodeType'))}
    <section class="sub_item episodeType_display">
        <h2 class="label">
            {translate key="plugins.generic.podcast.episodeType"}
        </h2>
        <div class="value">
            {translate key=("plugins.generic.podcast.episodeType."|cat:($publication->getData('episodeType')))}
        </div>
    </section>
    {/if}

    {if !empty($publication->getData('episodeNumber'))}
    <section class="sub_item episodeNumber_display">
        <h2 class="label">
            {translate key="plugins.generic.podcast.episodeNumber"}
        </h2>
        <div class="value">
            {$publication->getData('episodeNumber')}
        </div>
    </section>
    {/if}

    {if !empty($publication->getData('duration'))}
    <section class="sub_item duration_display">
        <h2 class="label">
            {translate key="plugins.generic.podcast.duration"}
        </h2>
        <div class="value">
            {$publication->getData('duration')|regex_replace:"/^[0:]+/":""}
        </div>
    </section>
    {/if}

    <section class="sub_item explicit_display">
        <h2 class="label">
            {translate key="plugins.generic.podcast.explicit"}
        </h2>
        <div class="value">
            {if $publication->getData('explicit')}
                {translate key="common.yes"}
            {else}
                {translate key="common.no"}
            {/if}
        </div>
    </section>
</div>