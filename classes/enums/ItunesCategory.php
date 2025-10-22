<?php

namespace APP\plugins\generic\podcast\classes\enums;

enum ItunesCategory: string
{
    case ARTS = 'Arts';

    case BUSINESS = 'Business';

    case COMEDY = 'Comedy';

    case EDUCATION = 'Education';

    case FICTION = 'Fiction';

    case GOVERNMENT = 'Government';

    case HISTORY = 'History';

    case HEALTH_FITNESS = 'Health & Fitness';

    case KIDS_FAMILY = 'Kids & Family';

    case LEISURE = 'Leisure';

    case MUSIC = 'Music';

    case NEWS = 'News';

    case RELIGION_SPIRITUALITY = 'Religion & Spirituality';

    case SCIENCE = 'Science';

    case SOCIETY_CULTURE = 'Society & Culture';

    case SPORTS = 'Sports';

    case TECHNOLOGY = 'Technology';

    case TRUE_CRIME = 'True Crime';

    case TV_FILM = 'TV & Film';

    public function labelKey(): string
    {
        return match ($this) {
            self::ARTS => 'plugins.generic.podcast.itunesCategory.arts',
            self::BUSINESS => 'plugins.generic.podcast.itunesCategory.business',
            self::COMEDY => 'plugins.generic.podcast.itunesCategory.comedy',
            self::EDUCATION => 'plugins.generic.podcast.itunesCategory.education',
            self::FICTION => 'plugins.generic.podcast.itunesCategory.fiction',
            self::GOVERNMENT => 'plugins.generic.podcast.itunesCategory.government',
            self::HISTORY => 'plugins.generic.podcast.itunesCategory.history',
            self::HEALTH_FITNESS => 'plugins.generic.podcast.itunesCategory.health_fitness',
            self::KIDS_FAMILY => 'plugins.generic.podcast.itunesCategory.kids_family',
            self::LEISURE => 'plugins.generic.podcast.itunesCategory.leisure',
            self::MUSIC => 'plugins.generic.podcast.itunesCategory.music',
            self::NEWS => 'plugins.generic.podcast.itunesCategory.news',
            self::RELIGION_SPIRITUALITY => 'plugins.generic.podcast.itunesCategory.religion_spirituality',
            self::SCIENCE => 'plugins.generic.podcast.itunesCategory.science',
            self::SOCIETY_CULTURE => 'plugins.generic.podcast.itunesCategory.society_culture',
            self::SPORTS => 'plugins.generic.podcast.itunesCategory.sports',
            self::TECHNOLOGY => 'plugins.generic.podcast.itunesCategory.technology',
            self::TRUE_CRIME => 'plugins.generic.podcast.itunesCategory.true_crime',
            self::TV_FILM => 'plugins.generic.podcast.itunesCategory.tv_film',
        };
    }

    public function label(?string $locale = null): string
    {
        return __($this->labelKey(), locale: $locale);
    }

    public function subcategories(): array
    {
        return match ($this) {
            self::ARTS => ItunesSubCategory::arts(),
            self::BUSINESS => ItunesSubCategory::business(),
            self::COMEDY => ItunesSubCategory::comedy(),
            self::EDUCATION => ItunesSubCategory::education(),
            self::FICTION => ItunesSubCategory::fiction(),
            self::GOVERNMENT => ItunesSubCategory::government(),
            self::HISTORY => ItunesSubCategory::history(),
            self::HEALTH_FITNESS => ItunesSubCategory::health_fitness(),
            self::KIDS_FAMILY => ItunesSubCategory::kids_family(),
            self::LEISURE => ItunesSubCategory::leisure(),
            self::MUSIC => ItunesSubCategory::music(),
            self::NEWS => ItunesSubCategory::news(),
            self::RELIGION_SPIRITUALITY => ItunesSubCategory::religion_spirituality(),
            self::SCIENCE => ItunesSubCategory::science(),
            self::SOCIETY_CULTURE => ItunesSubCategory::society_culture(),
            self::SPORTS => ItunesSubCategory::sports(),
            self::TECHNOLOGY => ItunesSubCategory::technology(),
            self::TRUE_CRIME => ItunesSubCategory::true_crime(),
            self::TV_FILM => ItunesSubCategory::tv_film(),
            default => [],
        };
    }
}
