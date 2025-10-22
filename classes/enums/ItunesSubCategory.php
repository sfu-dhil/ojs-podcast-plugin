<?php

namespace APP\plugins\generic\podcast\classes\enums;

enum ItunesSubCategory: string
{
    // Arts
    case ARTS_BOOKS = 'Books';

    case ARTS_DESIGN = 'Design';

    case ARTS_FASHION_BEAUTY = 'Fashion & Beauty';

    case ARTS_FOOD = 'Food';

    case ARTS_PERFORMING_ARTS = 'Performing Arts';

    case ARTS_VISUAL_ARTS = 'Visual Arts';

    // Business
    case BUSINESS_CAREERS = 'Careers';

    case BUSINESS_ENTREPRENEURSHIP = 'Entrepreneurship';

    case BUSINESS_INVESTING = 'Investing';

    case BUSINESS_MANAGEMENT = 'Management';

    case BUSINESS_MARKETING = 'Marketing';

    case BUSINESS_NON_PROFIT = 'Non-Profit';

    // Comedy
    case COMEDY_INTERVIEWS = 'Comedy Interviews';

    case COMEDY_IMPROV = 'Improv';

    case COMEDY_STAND_UP = 'Stand-Up';

    // Education
    case EDUCATION_COURSES = 'Courses';

    case EDUCATION_HOW_TO = 'How To';

    case EDUCATION_LANGUAGE_LEARNING = 'Language Learning';

    case EDUCATION_SELF_IMPROVEMENT = 'Self-Improvement';

    // Fiction
    case FICTION_COMEDY_FICTION = 'Comedy Fiction';

    case FICTION_DRAMA = 'Drama';

    case FICTION_SCIENCE_FICTION = 'Science Fiction';

    // Health & Fitness
    case HEALTH_FITNESS_ALTERNATIVE_HEALTH = 'Alternative Health';

    case HEALTH_FITNESS_FITNESS = 'Fitness';

    case HEALTH_FITNESS_MEDICINE = 'Medicine';

    case HEALTH_FITNESS_MENTAL_HEALTH = 'Mental Health';

    case HEALTH_FITNESS_NUTRITION = 'Nutrition';

    case HEALTH_FITNESS_SEXUALITY = 'Sexuality';

    // Kids & Family
    case KIDS_FAMILY_EDUCATION_FOR_KIDS = 'Education for Kids';

    case KIDS_FAMILY_PARENTING = 'Parenting';

    case KIDS_FAMILY_PETS_ANIMALS = 'Pets & Animals';

    case KIDS_FAMILY_STORIES_FOR_KIDS = 'Stories for Kids';

    // Leisure
    case LEISURE_ANIMATION_MANGA = 'Animation & Manga';

    case LEISURE_AUTOMOTIVE = 'Automotive';

    case LEISURE_AVIATION = 'Aviation';

    case LEISURE_CRAFTS = 'Crafts';

    case LEISURE_GAMES = 'Games';

    case LEISURE_HOBBIES = 'Hobbies';

    case LEISURE_HOME_GARDEN = 'Home & Garden';

    case LEISURE_VIDEO_GAMES = 'Video Games';

    // Music
    case MUSIC_COMMENTARY = 'Music Commentary';

    case MUSIC_HISTORY = 'Music History';

    case MUSIC_INTERVIEWS = 'Music Interviews';

    // News
    case NEWS_BUSINESS = 'Business News';

    case NEWS_DAILY = 'Daily News';

    case NEWS_ENTERTAINMENT = 'Entertainment News';

    case NEWS_COMMENTARY = 'News Commentary';

    case NEWS_POLITICS = 'Politics';

    case NEWS_SPORTS = 'Sports News';

    case NEWS_TECH = 'Tech News';

    // Religion & Spirituality
    case RELIGION_SPIRITUALITY_BUDDHISM = 'Buddhism';

    case RELIGION_SPIRITUALITY_CHRISTIANITY = 'Christianity';

    case RELIGION_SPIRITUALITY_HINDUISM = 'Hinduism';

    case RELIGION_SPIRITUALITY_ISLAM = 'Islam';

    case RELIGION_SPIRITUALITY_JUDAISM = 'Judaism';

    case RELIGION_SPIRITUALITY_RELIGION = 'Religion';

    case RELIGION_SPIRITUALITY_SPIRITUALITY = 'Spirituality';

    // Science
    case SCIENCE_ASTRONOMY = 'Astronomy';

    case SCIENCE_CHEMISTRY = 'Chemistry';

    case SCIENCE_EARTH_SCIENCES = 'Earth Sciences';

    case SCIENCE_LIFE_SCIENCES = 'Life Sciences';

    case SCIENCE_MATHEMATICS = 'Mathematics';

    case SCIENCE_NATURAL_SCIENCES = 'Natural Sciences';

    case SCIENCE_NATURE = 'Nature';

    case SCIENCE_PHYSICS = 'Physics';

    case SCIENCE_SOCIAL_SCIENCES = 'Social Sciences';

    // Society & Culture
    case SOCIETY_CULTURE_DOCUMENTARY = 'Documentary';

    case SOCIETY_CULTURE_PERSONAL_JOURNALS = 'Personal Journals';

    case SOCIETY_CULTURE_PHILOSOPHY = 'Philosophy';

    case SOCIETY_CULTURE_PLACES_TRAVEL = 'Places & Travel';

    case SOCIETY_CULTURE_RELATIONSHIPS = 'Relationships';

    // Sports
    case SPORTS_BASEBALL = 'Baseball';

    case SPORTS_BASKETBALL = 'Basketball';

    case SPORTS_CRICKET = 'Cricket';

    case SPORTS_FANTASY_SPORTS = 'Fantasy Sports';

    case SPORTS_FOOTBALL = 'Football';

    case SPORTS_GOLF = 'Golf';

    case SPORTS_HOCKEY = 'Hockey';

    case SPORTS_RUGBY = 'Rugby';

    case SPORTS_RUNNING = 'Running';

    case SPORTS_SOCCER = 'Soccer';

    case SPORTS_SWIMMING = 'Swimming';

    case SPORTS_TENNIS = 'Tennis';

    case SPORTS_VOLLEYBALL = 'Volleyball';

    case SPORTS_WILDERNESS = 'Wilderness';

    case SPORTS_WRESTLING = 'Wrestling';

    // TV & Film
    case TV_FILM_AFTER_SHOWS = 'After Shows';

    case TV_FILM_FILM_HISTORY = 'Film History';

    case TV_FILM_FILM_INTERVIEWS = 'Film Interviews';

    case TV_FILM_FILM_REVIEWS = 'Film Reviews';

    case TV_FILM_TV_REVIEWS = 'TV Reviews';

    public static function arts(): array
    {
        return [
            self::ARTS_BOOKS,
            self::ARTS_DESIGN,
            self::ARTS_FASHION_BEAUTY,
            self::ARTS_FOOD,
            self::ARTS_PERFORMING_ARTS,
            self::ARTS_VISUAL_ARTS,
        ];
    }

    public static function business(): array
    {
        return [
            self::BUSINESS_CAREERS,
            self::BUSINESS_ENTREPRENEURSHIP,
            self::BUSINESS_INVESTING,
            self::BUSINESS_MANAGEMENT,
            self::BUSINESS_MARKETING,
            self::BUSINESS_NON_PROFIT,
        ];
    }

    public static function comedy(): array
    {
        return [
            self::COMEDY_INTERVIEWS,
            self::COMEDY_IMPROV,
            self::COMEDY_STAND_UP,
        ];
    }

    public static function education(): array
    {
        return [
            self::EDUCATION_COURSES,
            self::EDUCATION_HOW_TO,
            self::EDUCATION_LANGUAGE_LEARNING,
            self::EDUCATION_SELF_IMPROVEMENT,
        ];
    }

    public static function fiction(): array
    {
        return [
            self::FICTION_COMEDY_FICTION,
            self::FICTION_DRAMA,
            self::FICTION_SCIENCE_FICTION,
        ];
    }

    public static function government(): array
    {
        return [];
    }

    public static function history(): array
    {
        return [];
    }

    public static function health_fitness(): array
    {
        return [
            self::HEALTH_FITNESS_ALTERNATIVE_HEALTH,
            self::HEALTH_FITNESS_FITNESS,
            self::HEALTH_FITNESS_MEDICINE,
            self::HEALTH_FITNESS_MENTAL_HEALTH,
            self::HEALTH_FITNESS_NUTRITION,
            self::HEALTH_FITNESS_SEXUALITY,
        ];
    }

    public static function kids_family(): array
    {
        return [
            self::KIDS_FAMILY_EDUCATION_FOR_KIDS,
            self::KIDS_FAMILY_PARENTING,
            self::KIDS_FAMILY_PETS_ANIMALS,
            self::KIDS_FAMILY_STORIES_FOR_KIDS,
        ];
    }

    public static function leisure(): array
    {
        return [
            self::LEISURE_ANIMATION_MANGA,
            self::LEISURE_AUTOMOTIVE,
            self::LEISURE_AVIATION,
            self::LEISURE_CRAFTS,
            self::LEISURE_GAMES,
            self::LEISURE_HOBBIES,
            self::LEISURE_HOME_GARDEN,
            self::LEISURE_VIDEO_GAMES,
        ];
    }

    public static function music(): array
    {
        return [
            self::MUSIC_COMMENTARY,
            self::MUSIC_HISTORY,
            self::MUSIC_INTERVIEWS,
        ];
    }

    public static function news(): array
    {
        return [
            self::NEWS_BUSINESS,
            self::NEWS_DAILY,
            self::NEWS_ENTERTAINMENT,
            self::NEWS_COMMENTARY,
            self::NEWS_POLITICS,
            self::NEWS_SPORTS,
            self::NEWS_TECH,
        ];
    }

    public static function religion_spirituality(): array
    {
        return [
            self::RELIGION_SPIRITUALITY_BUDDHISM,
            self::RELIGION_SPIRITUALITY_CHRISTIANITY,
            self::RELIGION_SPIRITUALITY_HINDUISM,
            self::RELIGION_SPIRITUALITY_ISLAM,
            self::RELIGION_SPIRITUALITY_JUDAISM,
            self::RELIGION_SPIRITUALITY_RELIGION,
            self::RELIGION_SPIRITUALITY_SPIRITUALITY,
        ];
    }

    public static function science(): array
    {
        return [
            self::SCIENCE_ASTRONOMY,
            self::SCIENCE_CHEMISTRY,
            self::SCIENCE_EARTH_SCIENCES,
            self::SCIENCE_LIFE_SCIENCES,
            self::SCIENCE_MATHEMATICS,
            self::SCIENCE_NATURAL_SCIENCES,
            self::SCIENCE_NATURE,
            self::SCIENCE_PHYSICS,
            self::SCIENCE_SOCIAL_SCIENCES,
        ];
    }

    public static function society_culture(): array
    {
        return [
            self::SOCIETY_CULTURE_DOCUMENTARY,
            self::SOCIETY_CULTURE_PERSONAL_JOURNALS,
            self::SOCIETY_CULTURE_PHILOSOPHY,
            self::SOCIETY_CULTURE_PLACES_TRAVEL,
            self::SOCIETY_CULTURE_RELATIONSHIPS,
        ];
    }

    public static function sports(): array
    {
        return [
            self::SPORTS_BASEBALL,
            self::SPORTS_BASKETBALL,
            self::SPORTS_CRICKET,
            self::SPORTS_FANTASY_SPORTS,
            self::SPORTS_FOOTBALL,
            self::SPORTS_GOLF,
            self::SPORTS_HOCKEY,
            self::SPORTS_RUGBY,
            self::SPORTS_RUNNING,
            self::SPORTS_SOCCER,
            self::SPORTS_SWIMMING,
            self::SPORTS_TENNIS,
            self::SPORTS_VOLLEYBALL,
            self::SPORTS_WILDERNESS,
            self::SPORTS_WRESTLING,
        ];
    }

    public static function technology(): array
    {
        return [];
    }

    public static function true_crime(): array
    {
        return [];
    }

    public static function tv_film(): array
    {
        return [
            self::TV_FILM_AFTER_SHOWS,
            self::TV_FILM_FILM_HISTORY,
            self::TV_FILM_FILM_INTERVIEWS,
            self::TV_FILM_FILM_REVIEWS,
            self::TV_FILM_TV_REVIEWS,
        ];
    }

    public function labelKey(): string
    {
        return match ($this) {
            // Arts
            self::ARTS_BOOKS => 'plugins.generic.podcast.itunesSubcategory.arts_books',
            self::ARTS_DESIGN => 'plugins.generic.podcast.itunesSubcategory.arts_design',
            self::ARTS_FASHION_BEAUTY => 'plugins.generic.podcast.itunesSubcategory.arts_fashion_beauty',
            self::ARTS_FOOD => 'plugins.generic.podcast.itunesSubcategory.arts_food',
            self::ARTS_PERFORMING_ARTS => 'plugins.generic.podcast.itunesSubcategory.arts_performing_arts',
            self::ARTS_VISUAL_ARTS => 'plugins.generic.podcast.itunesSubcategory.arts_visual_arts',
            // Business
            self::BUSINESS_CAREERS => 'plugins.generic.podcast.itunesSubcategory.business_careers',
            self::BUSINESS_ENTREPRENEURSHIP => 'plugins.generic.podcast.itunesSubcategory.business_entrepreneurship',
            self::BUSINESS_INVESTING => 'plugins.generic.podcast.itunesSubcategory.business_investing',
            self::BUSINESS_MANAGEMENT => 'plugins.generic.podcast.itunesSubcategory.business_management',
            self::BUSINESS_MARKETING => 'plugins.generic.podcast.itunesSubcategory.business_marketing',
            self::BUSINESS_NON_PROFIT => 'plugins.generic.podcast.itunesSubcategory.business_non_profit',
            // Comedy
            self::COMEDY_INTERVIEWS => 'plugins.generic.podcast.itunesSubcategory.comedy_interviews',
            self::COMEDY_IMPROV => 'plugins.generic.podcast.itunesSubcategory.comedy_improv',
            self::COMEDY_STAND_UP => 'plugins.generic.podcast.itunesSubcategory.comedy_stand_up',
            // Education
            self::EDUCATION_COURSES => 'plugins.generic.podcast.itunesSubcategory.education_courses',
            self::EDUCATION_HOW_TO => 'plugins.generic.podcast.itunesSubcategory.education_how_to',
            self::EDUCATION_LANGUAGE_LEARNING => 'plugins.generic.podcast.itunesSubcategory.education_language_learning',
            self::EDUCATION_SELF_IMPROVEMENT => 'plugins.generic.podcast.itunesSubcategory.education_self_improvement',
            // Fiction
            self::FICTION_COMEDY_FICTION => 'plugins.generic.podcast.itunesSubcategory.fiction_comedy_fiction',
            self::FICTION_DRAMA => 'plugins.generic.podcast.itunesSubcategory.fiction_drama',
            self::FICTION_SCIENCE_FICTION => 'plugins.generic.podcast.itunesSubcategory.fiction_science_fiction',
            // Health & Fitness
            self::HEALTH_FITNESS_ALTERNATIVE_HEALTH => 'plugins.generic.podcast.itunesSubcategory.health_fitness_alternative_health',
            self::HEALTH_FITNESS_FITNESS => 'plugins.generic.podcast.itunesSubcategory.health_fitness_fitness',
            self::HEALTH_FITNESS_MEDICINE => 'plugins.generic.podcast.itunesSubcategory.health_fitness_medicine',
            self::HEALTH_FITNESS_MENTAL_HEALTH => 'plugins.generic.podcast.itunesSubcategory.health_fitness_mental_health',
            self::HEALTH_FITNESS_NUTRITION => 'plugins.generic.podcast.itunesSubcategory.health_fitness_nutrition',
            self::HEALTH_FITNESS_SEXUALITY => 'plugins.generic.podcast.itunesSubcategory.health_fitness_sexuality',
            // Kids & Family
            self::KIDS_FAMILY_EDUCATION_FOR_KIDS => 'plugins.generic.podcast.itunesSubcategory.kids_family_education_for_kids',
            self::KIDS_FAMILY_PARENTING => 'plugins.generic.podcast.itunesSubcategory.kids_family_parenting',
            self::KIDS_FAMILY_PETS_ANIMALS => 'plugins.generic.podcast.itunesSubcategory.kids_family_pets_animals',
            self::KIDS_FAMILY_STORIES_FOR_KIDS => 'plugins.generic.podcast.itunesSubcategory.kids_family_stories_for_kids',
            // Leisure
            self::LEISURE_ANIMATION_MANGA => 'plugins.generic.podcast.itunesSubcategory.leisure_animation_manga',
            self::LEISURE_AUTOMOTIVE => 'plugins.generic.podcast.itunesSubcategory.leisure_automotive',
            self::LEISURE_AVIATION => 'plugins.generic.podcast.itunesSubcategory.leisure_aviation',
            self::LEISURE_CRAFTS => 'plugins.generic.podcast.itunesSubcategory.leisure_crafts',
            self::LEISURE_GAMES => 'plugins.generic.podcast.itunesSubcategory.leisure_games',
            self::LEISURE_HOBBIES => 'plugins.generic.podcast.itunesSubcategory.leisure_hobbies',
            self::LEISURE_HOME_GARDEN => 'plugins.generic.podcast.itunesSubcategory.leisure_home_garden',
            self::LEISURE_VIDEO_GAMES => 'plugins.generic.podcast.itunesSubcategory.leisure_video_games',
            // Music
            self::MUSIC_COMMENTARY => 'plugins.generic.podcast.itunesSubcategory.music_commentary',
            self::MUSIC_HISTORY => 'plugins.generic.podcast.itunesSubcategory.music_history',
            self::MUSIC_INTERVIEWS => 'plugins.generic.podcast.itunesSubcategory.music_interviews',
            // News
            self::NEWS_BUSINESS => 'plugins.generic.podcast.itunesSubcategory.news_business',
            self::NEWS_DAILY => 'plugins.generic.podcast.itunesSubcategory.news_daily',
            self::NEWS_ENTERTAINMENT => 'plugins.generic.podcast.itunesSubcategory.news_entertainment',
            self::NEWS_COMMENTARY => 'plugins.generic.podcast.itunesSubcategory.news_commentary',
            self::NEWS_POLITICS => 'plugins.generic.podcast.itunesSubcategory.news_politics',
            self::NEWS_SPORTS => 'plugins.generic.podcast.itunesSubcategory.news_sports',
            self::NEWS_TECH => 'plugins.generic.podcast.itunesSubcategory.news_tech',
            // Religion & Spirituality
            self::RELIGION_SPIRITUALITY_BUDDHISM => 'plugins.generic.podcast.itunesSubcategory.religion_spirituality_buddhism',
            self::RELIGION_SPIRITUALITY_CHRISTIANITY => 'plugins.generic.podcast.itunesSubcategory.religion_spirituality_christianity',
            self::RELIGION_SPIRITUALITY_HINDUISM => 'plugins.generic.podcast.itunesSubcategory.religion_spirituality_hinduism',
            self::RELIGION_SPIRITUALITY_ISLAM => 'plugins.generic.podcast.itunesSubcategory.religion_spirituality_islam',
            self::RELIGION_SPIRITUALITY_JUDAISM => 'plugins.generic.podcast.itunesSubcategory.religion_spirituality_judaism',
            self::RELIGION_SPIRITUALITY_RELIGION => 'plugins.generic.podcast.itunesSubcategory.religion_spirituality_religion',
            self::RELIGION_SPIRITUALITY_SPIRITUALITY => 'plugins.generic.podcast.itunesSubcategory.religion_spirituality_spirituality',
            // Science
            self::SCIENCE_ASTRONOMY => 'plugins.generic.podcast.itunesSubcategory.science_astronomy',
            self::SCIENCE_CHEMISTRY => 'plugins.generic.podcast.itunesSubcategory.science_chemistry',
            self::SCIENCE_EARTH_SCIENCES => 'plugins.generic.podcast.itunesSubcategory.science_earth_sciences',
            self::SCIENCE_LIFE_SCIENCES => 'plugins.generic.podcast.itunesSubcategory.science_life_sciences',
            self::SCIENCE_MATHEMATICS => 'plugins.generic.podcast.itunesSubcategory.science_mathematics',
            self::SCIENCE_NATURAL_SCIENCES => 'plugins.generic.podcast.itunesSubcategory.science_natural_sciences',
            self::SCIENCE_NATURE => 'plugins.generic.podcast.itunesSubcategory.science_nature',
            self::SCIENCE_PHYSICS => 'plugins.generic.podcast.itunesSubcategory.science_physics',
            self::SCIENCE_SOCIAL_SCIENCES => 'plugins.generic.podcast.itunesSubcategory.science_social_sciences',
            // Society & Culture
            self::SOCIETY_CULTURE_DOCUMENTARY => 'plugins.generic.podcast.itunesSubcategory.society_culture_documentary',
            self::SOCIETY_CULTURE_PERSONAL_JOURNALS => 'plugins.generic.podcast.itunesSubcategory.society_culture_personal_journals',
            self::SOCIETY_CULTURE_PHILOSOPHY => 'plugins.generic.podcast.itunesSubcategory.society_culture_philosophy',
            self::SOCIETY_CULTURE_PLACES_TRAVEL => 'plugins.generic.podcast.itunesSubcategory.society_culture_places_travel',
            self::SOCIETY_CULTURE_RELATIONSHIPS => 'plugins.generic.podcast.itunesSubcategory.society_culture_relationships',
            // Sports
            self::SPORTS_BASEBALL => 'plugins.generic.podcast.itunesSubcategory.sports_baseball',
            self::SPORTS_BASKETBALL => 'plugins.generic.podcast.itunesSubcategory.sports_basketball',
            self::SPORTS_CRICKET => 'plugins.generic.podcast.itunesSubcategory.sports_cricket',
            self::SPORTS_FANTASY_SPORTS => 'plugins.generic.podcast.itunesSubcategory.sports_fantasy_sports',
            self::SPORTS_FOOTBALL => 'plugins.generic.podcast.itunesSubcategory.sports_football',
            self::SPORTS_GOLF => 'plugins.generic.podcast.itunesSubcategory.sports_golf',
            self::SPORTS_HOCKEY => 'plugins.generic.podcast.itunesSubcategory.sports_hockey',
            self::SPORTS_RUGBY => 'plugins.generic.podcast.itunesSubcategory.sports_rugby',
            self::SPORTS_RUNNING => 'plugins.generic.podcast.itunesSubcategory.sports_running',
            self::SPORTS_SOCCER => 'plugins.generic.podcast.itunesSubcategory.sports_soccer',
            self::SPORTS_SWIMMING => 'plugins.generic.podcast.itunesSubcategory.sports_swimming',
            self::SPORTS_TENNIS => 'plugins.generic.podcast.itunesSubcategory.sports_tennis',
            self::SPORTS_VOLLEYBALL => 'plugins.generic.podcast.itunesSubcategory.sports_volleyball',
            self::SPORTS_WILDERNESS => 'plugins.generic.podcast.itunesSubcategory.sports_wilderness',
            self::SPORTS_WRESTLING => 'plugins.generic.podcast.itunesSubcategory.sports_wrestling',
            // TV & Film
            self::TV_FILM_AFTER_SHOWS => 'plugins.generic.podcast.itunesSubcategory.tv_film_after_shows',
            self::TV_FILM_FILM_HISTORY => 'plugins.generic.podcast.itunesSubcategory.tv_film_film_history',
            self::TV_FILM_FILM_INTERVIEWS => 'plugins.generic.podcast.itunesSubcategory.tv_film_film_interviews',
            self::TV_FILM_FILM_REVIEWS => 'plugins.generic.podcast.itunesSubcategory.tv_film_film_reviews',
            self::TV_FILM_TV_REVIEWS => 'plugins.generic.podcast.itunesSubcategory.tv_film_tv_reviews',
        };
    }

    public function label(?string $locale = null): string
    {
        return __($this->labelKey(), locale: $locale);
    }
}
