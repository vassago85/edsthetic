<?php

namespace Tests\Feature;

use App\Enums\ProjectCategory;
use App\Enums\TileSize;
use App\Livewire\GalleryGrid;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GalleryFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_projects_can_be_filtered_by_category(): void
    {
        Project::factory()->published()->create([
            'title' => 'First wardrobe',
            'category' => ProjectCategory::Builtin,
            'tile_size' => TileSize::Tall,
            'sort_order' => 1,
        ]);

        Project::factory()->published()->create([
            'title' => 'Second kitchen',
            'category' => ProjectCategory::Kitchen,
            'tile_size' => TileSize::Wide,
            'sort_order' => 2,
        ]);

        Project::factory()->create([
            'title' => 'Hidden vanity',
            'category' => ProjectCategory::Vanity,
            'is_published' => false,
        ]);

        $component = Livewire::test(GalleryGrid::class)
            ->assertSeeInOrder(['First wardrobe', 'Second kitchen'])
            ->assertDontSee('Hidden vanity')
            ->assertSee('Kitchens')
            ->assertSee('Built-ins')
            ->assertDontSee('Vanities')
            ->assertSee('class="tile tall"', false)
            ->assertSee('class="tile wide"', false)
            ->call('setFilter', ProjectCategory::Kitchen->value);

        $html = $component->html();

        $this->assertMatchesRegularExpression(
            '/<button(?![^>]*\bhidden\b)[^>]*aria-label="View Second kitchen"/',
            $html,
        );
        $this->assertMatchesRegularExpression(
            '/<button(?=[^>]*\bhidden\b)[^>]*aria-label="View First wardrobe"/',
            $html,
        );
        $component->assertDontSee('Hidden vanity');
    }
}
