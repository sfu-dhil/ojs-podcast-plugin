<!DOCTYPE html>
<html lang="{$currentLocale|replace:"_":"-"}" xml:lang="{$currentLocale|replace:"_":"-"}">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={$defaultCharset|escape}" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>
	{if $isTitleHtml}
		{translate key="article.pageTitle" title=$title|strip_tags|escape}
	{else}
		{translate key="article.pageTitle" title=$title|escape}
	{/if}
	</title>

	{load_header context="frontend" headers=$headers}
	{load_stylesheet context="frontend" stylesheets=$stylesheets}
	{load_script context="frontend" scripts=$scripts}
</head>
<body class="pkp_page_{$requestedPage|escape} pkp_op_{$requestedOp|escape}">

	{* Header wrapper *}
	<header class="header_view">

		<a href="{$parentUrl}" class="return">
			<span class="pkp_screen_reader">
				{if $issue}
					{translate key="issue.return"}
				{else}
					{translate key="article.return"}
				{/if}
			</span>
		</a>

		<a href="{$parentUrl}" class="title">
			{if $isTitleHtml}
				{$title|strip_unsafe_html}
			{else}
				{$title|escape}
			{/if}
		</a>

		<a href="{$audioUrl}" class="download" download="download">
			<span class="label">
				{translate key="common.download"}
			</span>
			<span class="pkp_screen_reader">
				{translate key="common.downloadAudio"}
			</span>
		</a>

	</header>

	<div class="galley_view{if !$isLatestPublication} galley_view_with_notice{/if}">
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
	</div>
	{call_hook name="Templates::Common::Footer::PageFooter"}
</body>
</html>
