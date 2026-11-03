<?php

use Database\Seeders\SiteContentSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        (new SiteContentSeeder)->run();
    }

    public function down(): void
    {
        //
    }
};
