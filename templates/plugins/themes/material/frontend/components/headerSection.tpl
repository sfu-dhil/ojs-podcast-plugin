<!-- https://github.com/madi-nuralin/material/blob/v3_1_0-1/templates/frontend/components/headerSection.tpl -->
<div class="relative overflow-hidden {if isset($homepageImageUrl)}bg-slate-900{else}bg-slate-900{/if} dark:bg-slate-900 _dark:-mb-32 _dark:mt-[-4.75rem] _dark:pb-32 _dark:pt-[4.75rem]">
   {if isset($homepageImageUrl)}
      <img alt="" fetchpriority="high" decoding="async" data-nimg="1" class="absolute  -mb-56 -mr-72 opacity-50" style="color:transparent;width:100%;height:100%" src="{$homepageImageUrl}">
   {else}
      <img alt="" fetchpriority="high" decoding="async" data-nimg="1" class="absolute  -mb-56 -mr-72 opacity-50" style="color:transparent" src="{$gradientImageUrl}">
   {/if}
   <div class="py-16 sm:px-2 lg:relative lg:px-0 lg:py-20">
      <div class="mx-auto grid max-w-2xl grid-cols-1 items-center gap-x-8 gap-y-16 px-4 lg:max-w-8xl lg:grid-cols-2 lg:px-8 xl:gap-x-16 xl:px-12">
         <div class="relative z-10 md:text-center lg:text-left">
            <!--img alt=""-->
            <div class="relative">
              <p class="inline font-extrabold {if isset($homepageImageUrl)}text-white{else}text-white{/if} dark:text-white bg-clip-text font-display text-5xl tracking-tight">
                {$displayPageHeaderTitle|escape}
              </p>
              {* HACK: Remove journal description from header since it is elsewhere on the page *}
              {* {if $activeTheme->getOption('showDescriptionInJournalIndex')}
                <div class="mt-3 text-2xl tracking-tight {if isset($homepageImageUrl)}text-slate-200{else}text-slate-400 line-clamp-3{/if}">
                  {$currentContext->getLocalizedData('description')}
                </div>
              {/if} *}
              <div class="mt-8 flex gap-4 md:justify-center lg:justify-start">
                {* HACK: Remove the make submission button from homepage *}
                {* <a class="rounded-full bg-{$activeTheme->getOption('baseColour')}-300 py-2 px-4 text-sm font-semibold text-slate-900 hover:bg-{$activeTheme->getOption('baseColour')}-200 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-{$activeTheme->getOption('baseColour')}-300/50 active:bg-{$activeTheme->getOption('baseColour')}-500" href="{url router=$smarty.const.ROUTE_PAGE page="about" op="submissions"}" role="button">
                  {translate key="plugins.themes.material.makeSubmission"}
                </a> *}
                <a class="rounded-full bg-slate-800 py-2 px-4 text-sm font-medium text-white hover:bg-slate-700 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white/50 active:text-slate-400" href="{url router=$smarty.const.ROUTE_PAGE page="issue" op="archive"}">
                  {translate key="archive.archives"}
                </a>
              </div>
            </div>
         </div>
         {if !isset($homepageImageUrl)}
            <div class="relative lg:static xl:pl-10">
               <!--div class="relative">
                  <div class="absolute inset-0 rounded-2xl bg-gradient-to-tr from-sky-300 via-sky-300/70 to-blue-300 opacity-10 blur-lg"></div>
                  <div class="absolute inset-0 rounded-2xl bg-gradient-to-tr from-sky-300 via-sky-300/70 to-blue-300 opacity-10"></div>
                  <div class="relative rounded-2xl bg-[#0A101F]/80 ring-1 ring-white/10 backdrop-blur">
                     <div class="absolute -top-px left-20 right-11 h-px bg-gradient-to-r from-sky-300/0 via-sky-300/70 to-sky-300/0"></div>
                     <div class="absolute -bottom-px left-11 right-20 h-px bg-gradient-to-r from-blue-400/0 via-blue-400 to-blue-400/0"></div>
                  </div>
               </div-->
            </div>
         {/if}
      </div>
   </div>
</div>