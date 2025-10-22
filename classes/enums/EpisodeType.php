<?php

namespace APP\plugins\generic\podcast\classes\enums;

enum EpisodeType: string
{
    case FULL = 'full';
    case BONUS = 'bonus';
    case TRAILER = 'trailer';

    public function labelKey(): string
    {
        return match ($this) {
            self::FULL => 'plugins.generic.podcast.episodeType.full',
            self::BONUS => 'plugins.generic.podcast.episodeType.bonus',
            self::TRAILER => 'plugins.generic.podcast.episodeType.trailer',
        };
    }

    public function order(): int
    {
        return match ($this) {
            self::FULL => 1,
            self::BONUS => 2,
            self::TRAILER => 3,
        };
    }

    public function label(?string $locale = null): string
    {
        return __($this->labelKey(), locale: $locale);
    }
}
