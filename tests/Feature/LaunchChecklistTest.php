<?php

namespace Tests\Feature;

use App\Enums\EnquiryStatus;
use App\Enums\TileSize;
use App\Filament\Pages\ManageBusinessSettings;
use App\Filament\Pages\ManageContentSettings;
use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Filament\Resources\Enquiries\Pages\EditEnquiry;
use App\Filament\Resources\Enquiries\Pages\ListEnquiries;
use App\Filament\Resources\Enquiries\Pages\ViewEnquiry;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\UserResource;
use App\Jobs\PruneEnquiries;
use App\Livewire\GalleryGrid;
use App\Models\Enquiry;
use App\Models\Project;
use App\Models\User;
use App\Settings\ContentSettings;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class LaunchChecklistTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_public_page_contains_every_section_and_hides_a_placeholder_whatsapp_link(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('id="top"', false)
            ->assertSee('id="work"', false)
            ->assertSee('id="owner"', false)
            ->assertSee('id="process"', false)
            ->assertSee('id="aftercare"', false)
            ->assertSee('id="terms"', false)
            ->assertSee('id="contact"', false)
            ->assertSee('href="#work"', false)
            ->assertSee('href="#aftercare"', false)
            ->assertSee('href="#terms"', false)
            ->assertSee('href="#contact"', false)
            ->assertSee('<dt>WhatsApp</dt>', false)
            ->assertSee('https://wa.me/27795117900', false)
            ->assertDontSee('id="gallery"', false)
            ->assertDontSee('href="#gallery"', false)
            ->assertDontSee('Stand-in. Replace by selecting a hero photo', false)
            ->assertDontSee('Draft wording. Have it checked', false);

        $this->get('/up')->assertOk();
    }

    public function test_the_admin_panel_requires_a_signed_in_user(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/users')->assertRedirect('/admin/login');
        $this->get('/admin/enquiries')->assertRedirect('/admin/login');
        $this->get('/admin/projects')->assertRedirect('/admin/login');
        $this->get('/admin/manage-business-settings')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk();

        $this->actingAs(User::factory()->create());

        $this->get('/admin')->assertOk();
        $this->get('/admin/users')->assertOk();
        $this->get('/admin/enquiries')->assertOk();
        $this->get('/admin/projects')->assertOk();
        $this->get('/admin/manage-business-settings')->assertOk();
        $this->get('/admin/manage-content-settings')->assertOk();
        $this->get('/admin/profile')->assertOk();
    }

    public function test_seeded_business_and_content_forms_can_be_saved(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ManageBusinessSettings::class)
            ->call('save')
            ->assertHasNoFormErrors();

        Livewire::test(ManageContentSettings::class)
            ->call('save')
            ->assertHasNoFormErrors()
            ->fillForm(['hero_heading' => 'Built in the workshop.'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Built in the workshop.', app(ContentSettings::class)->hero_heading);
    }

    public function test_opening_an_enquiry_marks_it_read_and_the_badge_counts_unread(): void
    {
        $this->actingAs(User::factory()->create());

        $unread = Enquiry::factory()->count(2)->create();
        Enquiry::factory()->create(['read_at' => now()]);

        $this->assertSame('2', EnquiryResource::getNavigationBadge());

        Livewire::test(ViewEnquiry::class, ['record' => $unread->first()->getKey()])
            ->assertOk();

        $this->assertNotNull($unread->first()->fresh()->read_at);
        $this->assertSame('1', EnquiryResource::getNavigationBadge());

        Livewire::test(EditEnquiry::class, ['record' => $unread->last()->getKey()])
            ->assertOk();

        $this->assertNotNull($unread->last()->fresh()->read_at);
        $this->assertNull(EnquiryResource::getNavigationBadge());
    }

    public function test_an_admin_can_change_enquiry_status_in_bulk(): void
    {
        $this->actingAs(User::factory()->create());

        $first = Enquiry::factory()->create();
        $second = Enquiry::factory()->create();

        Livewire::test(ListEnquiries::class)
            ->callTableBulkAction('status', [$first, $second], ['status' => EnquiryStatus::Quoted->value])
            ->assertHasNoTableBulkActionErrors();

        $this->assertSame(EnquiryStatus::Quoted, $first->fresh()->status);
        $this->assertSame(EnquiryStatus::Quoted, $second->fresh()->status);
    }

    public function test_an_admin_cannot_delete_their_own_account_and_a_blank_password_is_kept(): void
    {
        $admin = User::factory()->create();
        $other = User::factory()->create();
        $hash = $other->password;

        $this->actingAs($admin);

        $this->assertFalse(UserResource::canDelete($admin));
        $this->assertTrue(UserResource::canDelete($other));

        Livewire::test(ListUsers::class)
            ->assertTableActionHidden('delete', $admin)
            ->assertTableActionVisible('delete', $other);

        Livewire::test(EditUser::class, ['record' => $other->getKey()])
            ->fillForm([
                'name' => 'Renamed Admin',
                'password' => '',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $other->refresh();
        $this->assertSame('Renamed Admin', $other->name);
        $this->assertSame($hash, $other->password);
    }

    public function test_the_first_admin_command_creates_a_user_and_rejects_a_duplicate(): void
    {
        $this->artisan('admin:create', [
            '--name' => 'Owner',
            '--email' => 'owner@esthetic.test',
            '--password' => 'owner-pass',
        ])->assertSuccessful();

        $owner = User::query()->where('email', 'owner@esthetic.test')->first();

        $this->assertNotNull($owner);
        $this->assertTrue(Hash::check('owner-pass', $owner->password));

        $this->artisan('admin:create', [
            '--name' => 'Owner',
            '--email' => 'owner@esthetic.test',
            '--password' => 'another-pass',
        ])->assertFailed();

        $this->artisan('admin:create', [
            '--name' => 'Short',
            '--email' => 'short@esthetic.test',
            '--password' => 'short',
        ])->assertFailed();

        $this->assertSame(1, User::query()->count());
    }

    public function test_the_gallery_viewer_walks_photos_and_ignores_unpublished_projects(): void
    {
        Storage::fake('public');

        $project = Project::factory()->published()->create([
            'title' => 'Oak kitchen',
            'tile_size' => TileSize::Large,
        ]);

        $bytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
        $first = storage_path('framework/testing/one.png');
        $second = storage_path('framework/testing/two.png');

        if (! is_dir(dirname($first))) {
            mkdir(dirname($first), 0777, true);
        }

        file_put_contents($first, $bytes);
        file_put_contents($second, $bytes);

        $project->addMedia($first)->toMediaCollection('photos');
        $project->addMedia($second)->toMediaCollection('photos');

        $hidden = Project::factory()->create([
            'title' => 'Hidden vanity',
            'is_published' => false,
        ]);

        Livewire::test(GalleryGrid::class)
            ->assertSee('class="tile wide tall"', false)
            ->call('open', $project->id)
            ->assertSet('viewingId', $project->id)
            ->assertSet('photoIndex', 0)
            ->assertSee('Oak kitchen')
            ->call('next')
            ->assertSet('photoIndex', 1)
            ->call('next')
            ->assertSet('photoIndex', 0)
            ->call('previous')
            ->assertSet('photoIndex', 1)
            ->call('close')
            ->assertSet('viewingId', null)
            ->call('open', $hidden->id)
            ->assertSet('viewingId', null)
            ->call('setFilter', 'not-a-category')
            ->assertSet('filter', 'all');
    }

    public function test_an_empty_gallery_has_no_filter_buttons(): void
    {
        Livewire::test(GalleryGrid::class)
            ->assertDontSee('aria-label="Filter gallery"', false);
    }

    public function test_phone_photos_lose_trailing_location_markers(): void
    {
        Storage::fake('public');

        $path = storage_path('framework/testing/phone.jpg');

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        $image = imagecreatetruecolor(16, 16);
        imagejpeg($image, $path);
        imagedestroy($image);
        file_put_contents($path, file_get_contents($path).'GPSLatitude');

        $project = Project::factory()->published()->create();
        $media = $project->addMedia($path)->toMediaCollection('photos');

        $this->assertStringNotContainsString('GPSLatitude', (string) file_get_contents($media->getPath()));
        $this->assertTrue($media->hasGeneratedConversion('thumb'));
        $this->assertTrue($media->hasGeneratedConversion('large'));
    }

    public function test_pruning_does_nothing_when_retention_is_disabled_and_the_command_is_scheduled(): void
    {
        $settings = app(ContentSettings::class);
        $settings->enquiry_retention_months = 0;
        $settings->save();

        Enquiry::factory()->create([
            'created_at' => now()->subYears(10),
        ]);

        $this->assertSame(0, app(PruneEnquiries::class)->handle());
        $this->assertDatabaseCount('enquiries', 1);

        $this->artisan('enquiries:prune')
            ->assertSuccessful()
            ->expectsOutputToContain('Deleted 0 enquiries.');

        $event = collect(app(Schedule::class)->events())->first(
            fn ($event): bool => str_contains((string) $event->command, 'enquiries:prune'),
        );

        $this->assertNotNull($event);
        $this->assertSame('10 2 * * *', $event->expression);
        $this->assertTrue($event->withoutOverlapping);
    }
}
