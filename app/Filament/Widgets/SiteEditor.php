<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\ManageBusinessSettings;
use App\Filament\Pages\ManageContentSettings;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteEditor extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int|array|null
    {
        return 4;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Business details', 'Edit')
                ->description('Name, phone, email, address, hours')
                ->icon(Heroicon::OutlinedBuildingOffice)
                ->url(ManageBusinessSettings::getUrl()),
            Stat::make('Page content', 'Edit')
                ->description('Hero, aftercare and terms')
                ->icon(Heroicon::OutlinedDocumentText)
                ->url(ManageContentSettings::getUrl()),
            Stat::make('Users', (string) User::query()->count())
                ->description('Add admins and change passwords')
                ->icon(Heroicon::OutlinedUsers)
                ->url(UserResource::getUrl()),
            Stat::make('Your password', 'Change')
                ->description(Filament::auth()->user()?->email)
                ->icon(Heroicon::OutlinedKey)
                ->url(Filament::getProfileUrl()),
        ];
    }
}
