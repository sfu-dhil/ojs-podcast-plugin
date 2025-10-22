<!DOCTYPE html>
<html lang="{$currentLocale|replace:"_":"-"}"
	xml:lang="{$currentLocale|replace:"_":"-"}"
	class="font-{$activeTheme->getOption('fontFamily')} h-full"
	{literal}
  		x-data="{ darkMode: localStorage.getItem('darkMode') || localStorage.setItem('darkMode', 'light') }"
  		x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
  		x-bind:class="{'dark': darkMode === 'dark' || (darkMode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)}"
  	{/literal}>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={$defaultCharset|escape}" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{translate key="article.pageTitle" title=$title|escape}</title>

	{load_header context="frontend" headers=$headers}
	{load_stylesheet context="frontend" stylesheets=$stylesheets}
	{load_script context="frontend" scripts=$scripts}
</head>

<body class="h-full bg-white dark:bg-slate-900 pkp_page_{$requestedPage|escape} pkp_op_{$requestedOp|escape}{if $showingLogo} has_site_logo{/if}" dir="{$currentLocaleLangDir|escape|default:"ltr"}">
	{* Header wrapper *}

	<header class="sticky top-0 z-50 flex flex-none flex-wrap items-center justify-between bg-white px-4 py-5 shadow-md shadow-slate-900/5 transition duration-500 sm:px-6 lg:px-8 dark:shadow-none dark:bg-slate-900/95 dark:backdrop-blur dark:[@supports(backdrop-filter:blur(0))]:bg-slate-900/75">

		<div class="flex items-center space-x-2">
			<div class="relative text-left">
				<a href="{$parentUrl}" class="flex h-8 items-center text-slate-500 justify-center rounded-xl shadow-md shadow-black/5 ring-1 ring-black/5 dark:bg-slate-700 dark:ring-inset dark:ring-white/5 px-3 text-sm dark:text-slate-400 dark:before:bg-slate-700 dark:hover:text-slate-300">
					{if $parent instanceOf Issue}
						{translate key="issue.return"}
					{else}
						{translate key="article.return"}
					{/if}
				</a>
			</div>
		</div>
		<div class="flex items-center space-x-2">
			<div class="relative text-left">
				<a href="{$parentUrl}" class="flex h-8 items-center text-slate-500 justify-center rounded-xl shadow-md shadow-black/5 ring-1 ring-black/5 dark:bg-slate-700 dark:ring-inset dark:ring-white/5 px-3 text-sm dark:text-slate-400 dark:before:bg-slate-700 dark:hover:text-slate-300">
					{$title|escape}
				</a>
			</div>
		</div>
		<div class="flex items-center space-x-2">
			<div class="relative text-left">
				<a href="{$audioUrl}" download class="flex h-8 items-center text-slate-500 justify-center rounded-xl shadow-md shadow-black/5 ring-1 ring-black/5 dark:bg-slate-700 dark:ring-inset dark:ring-white/5 px-3 text-sm dark:text-slate-400 dark:before:bg-slate-700 dark:hover:text-slate-300">
					{translate key="common.download"}
				</a>
			</div>
			{include file="frontend/components/ui/material_theme_selector.tpl"}
		</div>
	</header>

	<div class="mx-auto flex w-full max-w-8xl flex-auto justify-center sm:px-2 lg:px-8 xl:px-12 galley_view{if !$isLatestPublication} galley_view_with_notice{/if}">
		{if !$isLatestPublication}
			<div class="galley_view_notice">
				<div class="galley_view_notice_message" role="alert">
					{$datePublished}
				</div>
			</div>
		{/if}
		<audio controls preload="metadata">
			<source src="{$audioUrl}" type="{$audioType}" />
		</audio>

		{* <iframe src="" width="100%" height="100%" style="min-height: 500px;" title="{$galleyTitle}" allowfullscreen webkitallowfullscreen></iframe> *}
	</div>
	{call_hook name="Templates::Common::Footer::PageFooter"}
</body>
</html>