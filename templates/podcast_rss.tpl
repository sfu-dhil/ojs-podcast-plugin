<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:podcast="https://podcastindex.org/namespace/1.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/">
    <channel>
        <atom:link href="{$feedUrl|strip}" rel="self" type="application/rss+xml" />
        <title>{$journal->getName($locale)|strip|escape:'html'}</title>
        <description><![CDATA[{$journal->getDescription($locale)|strip}]]></description>
        <link>{url context=$journal->getPath()}</link>
        <language>{$locale|escape}</language>
        {foreach from=$itunesCategories item=category}
            <itunes:category text="{$category['label']|strip|escape:'html'}">
                {foreach from=$category['subcategories'] item=subcategory}
                    <itunes:category text="{$subcategory|strip|escape:'html'}" />
                {/foreach}
            </itunes:category>
        {/foreach}
        <itunes:explicit>{if $journal->getData('explicit')}true{else}false{/if}</itunes:explicit>
        {assign var='thumb' value=$journal->getData('journalThumbnail', $locale)}
        {if $thumb && $thumb.uploadName}
            <itunes:image href="{$journalPublicFilesUrl}/{$thumb.uploadName|escape:'url'}" />
        {/if}
        <podcast:locked>no</podcast:locked>
        {if !is_null($journal->getData('rssGuid')) }
            <podcast:guid>{$journal->getData('rssGuid')|escape}</podcast:guid>
        {/if}
        {foreach from=$journalAuthors item=author}
            <itunes:author>{$author|strip|escape:'html'}</itunes:author>
        {/foreach}
        {if !is_null($journal->getData('licenseTerms', $locale)) }
            <copyright><![CDATA[{$journal->getData('licenseTerms', $locale)|strip}]]></copyright>
        {/if}
        {* <podcast:txt purpose="verify"></podcast:txt> *}
        {* <podcast:funding url=""></podcast:funding> *}
        <itunes:type>episodic</itunes:type>
        {* <itunes:complete>yes</itunes:complete> *}
        <generator>Open Journal Systems (OJS) {$applicationVersion|strip} - PodcastPlugin {$pluginVersion|strip}</generator>
        {if $journal->getData('contactEmail') || $journal->getData('contactName') }
            <itunes:owner>
                {if $journal->getData('contactName') }
                    <itunes:name>{$journal->getData('contactName')|strip}</itunes:name>
                {/if}
                {if $journal->getData('contactEmail') }
                    <itunes:email>{$journal->getData('contactEmail')|strip}</itunes:email>
                {/if}
            </itunes:owner>
        {elseif $journal->getData('supportEmail') || $journal->getData('supportName') }
            <itunes:owner>
                {if $journal->getData('supportName') }
                    <itunes:name>{$journal->getData('supportName')|strip}</itunes:name>
                {/if}
                {if $journal->getData('supportEmail') }
                    <itunes:email>{$journal->getData('supportEmail')|strip}</itunes:email>
                {/if}
            </itunes:owner>
        {/if}
        <pubDate>{$latestDate|date_format:$smarty.const.DATE_RSS}</pubDate>
        <lastBuildDate>{$latestDate|date_format:$smarty.const.DATE_RSS}</lastBuildDate>
        <ttl>60</ttl>

        {foreach from=$items item=item}
            <item>
                <title>{$item.publication->getData('title', $locale)|strip|escape:'html'}</title>
                <enclosure length="{$item.audioFilesize|strip}" type="{$item.audioGalley->getFileType()}" url="{url page="article" op="download" path=$item.submission->getBestId()|to_array:$item.audioGalley->getBestGalleyId():$item.audioGalley->getData('submissionFileId') inline=true}" />
                <guid isPermaLink="true">{url page='article' op='view' path=$item.publication->getData('urlPath')|default:$item.submission->getId()}</guid>
                <link>{url page='article' op='view' path=$item.publication->getData('urlPath')|default:$item.submission->getId()}</link>
                <pubDate>{$item.publication->getData('datePublished')|date_format:$smarty.const.DATE_RSS}</pubDate>
                {if $item.publication->getData('abstract', $locale) }
                    <description><![CDATA[{$item.publication->getData('abstract', $locale)|strip}]]></description>
                {/if}
                {if !is_null($item.durationInSeconds) }
                    <itunes:duration>{$item.durationInSeconds|strip}</itunes:duration>
                {/if}
                {assign var='coverImage' value=$item.publication->getData('coverImage', $locale)}
                {if $coverImage && $coverImage.uploadName}
                    <itunes:image href="{$journalPublicFilesUrl}/{$coverImage.uploadName|escape:'url'}" />
                {/if}
                    <itunes:explicit>{if $item.publication->getData('explicit')}true{else}false{/if}</itunes:explicit>
                {foreach from=$item.transcriptGalleys item=transcriptGalley}
                    <podcast:transcript url="{url page="article" op="download" path=$item.submission->getBestId()|to_array:$transcriptGalley->getBestGalleyId():$transcriptGalley->getData('submissionFileId')}" type="{$transcriptGalley->getFileType()}" />
                {/foreach}
                {if !is_null($item.publication->getData('episodeNumber')) }
                    <itunes:episode>{$item.publication->getData('episodeNumber')|strip|escape:'html'}</itunes:episode>
                {/if}
                {if !is_null($item.issue->getNumber()) }
                    <itunes:season>{$item.issue->getNumber()|strip|escape:'html'}</itunes:season>
                {/if}
                {if !is_null($item.publication->getData('episodeType')) }
                    <itunes:episodeType>{$item.publication->getData('episodeType')|strip}</itunes:episodeType>
                {/if}
                {* <itunes:block>yes</itunes:block> *}
                {foreach from=$item.publicationAuthors item=author}
                    <itunes:author>{$author|strip|escape:'html'}</itunes:author>
                {/foreach}
                {foreach from=$item.imageGalleys item=imageGalley}
                    <media:content medium="image" url="{url page="article" op="download" path=$item.submission->getBestId()|to_array:$imageGalley->getBestGalleyId():$imageGalley->getData('submissionFileId')}" />
                {/foreach}
                {if $item.publication->getData('keywords', $locale) }
                    {foreach from=$item.publication->getData('keywords', $locale) item=keyword}
                        <itunes:keywords>{$keyword|strip|escape:'html'}</itunes:keywords>
                    {/foreach}
                {/if}
                {if !is_null($item.publication->getData('subtitle', $locale)) }
                    <itunes:subtitle>{$item.publication->getData('subtitle', $locale)|strip|escape:'html'}</itunes:subtitle>
                {/if}
                <category>Podcast</category>
            </item>
        {/foreach}
    </channel>
</rss>