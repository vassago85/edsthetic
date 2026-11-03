<?php

namespace Tests\Feature;

use App\Jobs\PruneEnquiries;
use App\Models\Enquiry;
use App\Settings\ContentSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PruneEnquiriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_enquiries_older_than_the_retention_period_are_deleted(): void
    {
        $settings = app(ContentSettings::class);
        $settings->enquiry_retention_months = 24;
        $settings->save();

        $expired = Enquiry::factory()->create([
            'created_at' => now()->subMonths(24)->subDay(),
        ]);
        $kept = Enquiry::factory()->create([
            'created_at' => now()->subMonths(23),
        ]);

        $deleted = app(PruneEnquiries::class)->handle();

        $this->assertSame(1, $deleted);
        $this->assertModelMissing($expired);
        $this->assertModelExists($kept);
    }

    public function test_retention_comes_from_settings(): void
    {
        $settings = app(ContentSettings::class);
        $settings->enquiry_retention_months = 6;
        $settings->save();

        $expired = Enquiry::factory()->create([
            'created_at' => now()->subMonths(6)->subDay(),
        ]);
        $kept = Enquiry::factory()->create([
            'created_at' => now()->subMonths(5),
        ]);

        app(PruneEnquiries::class)->handle();

        $this->assertModelMissing($expired);
        $this->assertModelExists($kept);
    }
}
