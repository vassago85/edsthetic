<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageBusinessSettings;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\User;
use App\Settings\BusinessSettings;
use Filament\Auth\Pages\EditProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_can_edit_business_details_and_other_users(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        Livewire::test(ManageBusinessSettings::class)
            ->assertOk()
            ->fillForm([
                'phone' => '082 555 0199',
                'email' => 'studio@esthetic.test',
                'notification_email' => 'jobs@esthetic.test',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('082 555 0199', app(BusinessSettings::class)->phone);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Second Admin',
                'email' => 'second@esthetic.test',
                'password' => 'secret-pass',
                'passwordConfirmation' => 'secret-pass',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $second = User::query()->where('email', 'second@esthetic.test')->first();
        $this->assertNotNull($second);
        $this->assertTrue(Hash::check('secret-pass', $second->password));

        Livewire::test(EditUser::class, ['record' => $second->getKey()])
            ->fillForm([
                'password' => 'changed-pass',
                'passwordConfirmation' => 'changed-pass',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue(Hash::check('changed-pass', $second->fresh()->password));

        Livewire::test(EditProfile::class)
            ->fillForm([
                'name' => 'Studio Owner',
                'email' => $admin->email,
                'password' => 'owner-pass1',
                'passwordConfirmation' => 'owner-pass1',
                'currentPassword' => 'password',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $admin->refresh();
        $this->assertSame('Studio Owner', $admin->name);
        $this->assertTrue(Hash::check('owner-pass1', $admin->password));
    }
}
