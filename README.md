# Podcast Plugin

## Schema/Assumptions

An OJS journal is treated as a podcast entity

An OJS issue is treated as a podcast season entity

The OJS article/submission/publication combination is treated as a podcast episode

## Plugin Label changes (English only)

The plugin will change change most labels with `journal` to `podcast`, `issue` to `season`, `article` to `episode`, and `reader` to `listener`:

There are also some database saved values including:
- The default Genre submission label from `Article Text` to `Podcast Audio File`.
- The default Section label from `Articles` to `Episodes`

The database saved labels will need to to manually changed if the podcast plugin is added to OJS with pre-existing content.

### (Optional) Manually update an existing journal's labels

`Settings` -> `Workflow` -> `Submission` -> `Components` -> `Article Text` -> `Edit` -> rename to `Podcast Audio File` -> `Save`

`Settings` -> `Journal` -> `Sections` -> `Articles` -> `Edit` -> rename to `Episodes` and change abbrev to `EP` -> `Save`


## (Optional) Themes

The Plugin is compatible with default basic them and the [Material Theme](https://github.com/madi-nuralin/material).

## Import Podcast via Rss Feed

The `PodcastRssImportPlugin` import will pull metadata & audio from the RSS feed. Warning: It will automatically set the content to be published.

```bash
php tools/importExport.php PodcastRssImportPlugin --journal-paidth=<JOURNAL ID> --rss-url=<RSS FEED URL>
# or
php tools/importExport.php PodcastRssImportPlugin --journal-path=<JOURNAL PATH> --rss-url=<RSS FEED URL>
```

After initial import, you can safely re-run omitting the `--rss-url` (change the `--rss-url` in the web UI if you need to fetch from another RSS feed).

```bash
php tools/importExport.php PodcastRssImportPlugin --journal-paidth=<JOURNAL ID>
# or
php tools/importExport.php PodcastRssImportPlugin --journal-path=<JOURNAL PATH>
```

### Optionally add common Podcast contributor roles

Add the `--add-contributor-roles` option when importing to add common Podcast contributor roles

```bash
php tools/importExport.php PodcastRssImportPlugin --journal-paidth=<JOURNAL ID> --rss-url=<RSS FEED URL> --add-contributor-roles
# or
php tools/importExport.php PodcastRssImportPlugin --journal-path=<JOURNAL PATH> --rss-url=<RSS FEED URL> --add-contributor-roles
```

<!--
## Release (currently not working well)

Add a Github personal token [https://help.github.com/en/github/authenticating-to-github/creating-a-personal-access-token-for-the-command-line
](https://help.github.com/en/github/authenticating-to-github/creating-a-personal-access-token-for-the-command-line) for cli usage

### Setup
```bash
npm install -g pkp-plugin-cli np
```

### Cut a new release
```bash
# np <SEMANTIC-VERSION>
pkp-plugin release podcast --newversion <SEMANTIC-VERSION-WITH-BUILD>
```

Example:
```bash
# np 1.0.0
pkp-plugin release podcast --newversion 1.0.0.0
```
-->