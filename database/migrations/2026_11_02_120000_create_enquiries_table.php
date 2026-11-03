<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('suburb', 120)->nullable();
            $table->string('job_type');
            $table->text('message')->nullable();
            $table->string('status')->default('new')->index();
            $table->text('internal_notes')->nullable();
            $table->timestamp('read_at')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('created_at');
            $table->index('job_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
