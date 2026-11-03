<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('enquiries', 'suburb')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table): void {
            $table->string('suburb', 120)->nullable()->after('email');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('enquiries', 'suburb')) {
            return;
        }

        Schema::table('enquiries', function (Blueprint $table): void {
            $table->dropColumn('suburb');
        });
    }
};
