<?php

namespace App\Jobs;

use App\Models\Enquiry;
use App\Settings\ContentSettings;

class PruneEnquiries
{
    public function handle(): int
    {
        $months = app(ContentSettings::class)->enquiry_retention_months;

        if ($months < 1) {
            return 0;
        }

        return Enquiry::query()
            ->where('created_at', '<', now()->subMonths($months))
            ->delete();
    }
}
