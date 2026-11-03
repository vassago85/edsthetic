<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TileSize: string implements HasLabel
{
    case Normal = 'normal';
    case Wide = 'wide';
    case Tall = 'tall';
    case Large = 'large';

    public function getLabel(): string
    {
        return match ($this) {
            self::Normal => 'Normal',
            self::Wide => 'Wide',
            self::Tall => 'Tall',
            self::Large => 'Large (wide and tall)',
        };
    }

    public function cssClass(): string
    {
        return match ($this) {
            self::Normal => '',
            self::Wide => 'wide',
            self::Tall => 'tall',
            self::Large => 'wide tall',
        };
    }
}
