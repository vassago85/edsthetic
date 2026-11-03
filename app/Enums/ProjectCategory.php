<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProjectCategory: string implements HasLabel
{
    case Kitchen = 'kitchen';
    case Builtin = 'builtin';
    case Vanity = 'vanity';
    case Custom = 'custom';

    public function getLabel(): string
    {
        return match ($this) {
            self::Kitchen => 'Kitchens',
            self::Builtin => 'Built-ins',
            self::Vanity => 'Vanities',
            self::Custom => 'Custom',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::Kitchen => 'Kitchen',
            self::Builtin => 'Built-in',
            self::Vanity => 'Vanity',
            self::Custom => 'Custom',
        };
    }
}
