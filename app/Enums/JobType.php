<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum JobType: string implements HasLabel
{
    case Kitchen = 'kitchen';
    case BuiltIn = 'builtin';
    case Facelift = 'facelift';
    case Vanity = 'vanity';
    case Storage = 'storage';
    case Office = 'office';
    case Doors = 'doors';
    case Maintenance = 'maintenance';
    case Shelving = 'shelving';
    case Carpentry = 'carpentry';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Kitchen => 'Kitchen',
            self::BuiltIn => 'Built-in cupboards',
            self::Facelift => 'Cabinet facelift',
            self::Vanity => 'Bathroom or makeup vanity',
            self::Storage => 'Heavy or smart storage',
            self::Office => 'Office cabinets or freestanding',
            self::Doors => 'Door hanging',
            self::Maintenance => 'Maintenance or repairs',
            self::Shelving => 'Shelving or study',
            self::Carpentry => 'General carpentry',
            self::Other => 'Something else',
        };
    }

    /**
     * @return list<self>
     */
    public static function forQuoteForm(): array
    {
        return [
            self::Kitchen,
            self::BuiltIn,
            self::Facelift,
            self::Vanity,
            self::Storage,
            self::Office,
            self::Doors,
            self::Maintenance,
            self::Other,
        ];
    }
}
