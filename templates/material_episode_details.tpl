
<div class="mt-0 border-t border-slate-200 dark:border-slate-800">
    <dl class="divide-y divide-slate-200 dark:divide-slate-800 my-0">
        {if !empty($publication->getData('episodeType'))}
        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="_text-sm/6 font-medium _text-slate-800">
                {translate key="plugins.generic.podcast.episodeType"}
            </dt>
            <dd class="mt-1 _text-sm/6 text-slate-400 sm:col-span-2 sm:mt-0">
                {translate key=("plugins.generic.podcast.episodeType."|cat:($publication->getData('episodeType')))}
            </dd>
        </div>
        {/if}

        {if !empty($publication->getData('episodeNumber'))}
        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="_text-sm/6 font-medium _text-slate-800">
                {translate key="plugins.generic.podcast.episodeNumber"}
            </dt>
            <dd class="mt-1 _text-sm/6 text-slate-400 sm:col-span-2 sm:mt-0">
                {$publication->getData('episodeNumber')}
            </dd>
        </div>
        {/if}

        {if !empty($publication->getData('duration'))}
        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="_text-sm/6 font-medium _text-slate-800">
                {translate key="plugins.generic.podcast.duration"}
            </dt>
            <dd class="mt-1 _text-sm/6 text-slate-400 sm:col-span-2 sm:mt-0">
                {$publication->getData('duration')|regex_replace:"/^[0:]+/":""}
            </dd>
        </div>
        {/if}

        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="_text-sm/6 font-medium _text-slate-800">
                {translate key="plugins.generic.podcast.explicit"}
            </dt>
            <dd class="mt-1 _text-sm/6 text-slate-400 sm:col-span-2 sm:mt-0">
                {if $publication->getData('explicit')}
                    {translate key="common.yes"}
                {else}
                    {translate key="common.no"}
                {/if}
            </dd>
        </div>
    </dl>
</div>