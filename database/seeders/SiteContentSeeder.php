<?php

namespace Database\Seeders;

use App\Support\SiteDefaults;
use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        SiteDefaults::persist();
    }
}
