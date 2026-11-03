<?php

namespace App\Console\Commands;

use App\Jobs\PruneEnquiries;
use Illuminate\Console\Command;

class PruneEnquiriesCommand extends Command
{
    protected $signature = 'enquiries:prune';

    protected $description = 'Delete enquiries older than the retention period (POPIA)';

    public function handle(PruneEnquiries $job): int
    {
        $deleted = $job->handle();

        $this->info("Deleted {$deleted} ".str('enquiry')->plural($deleted).'.');

        return self::SUCCESS;
    }
}
