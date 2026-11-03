<?php

namespace App\Filament\Widgets;

use App\Enums\EnquiryStatus;
use App\Models\Enquiry;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EnquiryStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make(
                'New enquiries',
                (string) Enquiry::query()->where('status', EnquiryStatus::New)->count(),
            ),
            Stat::make(
                'Enquiries this month',
                (string) Enquiry::query()->where('created_at', '>=', now()->startOfMonth())->count(),
            ),
        ];
    }
}
