<?php

namespace Database\Factories;

use App\Enums\ProjectCategory;
use App\Enums\TileSize;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'title' => Str::title($title),
            'slug' => Str::slug($title).'-'.fake()->unique()->numerify('###'),
            'category' => fake()->randomElement(ProjectCategory::cases()),
            'description' => fake()->optional()->sentence(),
            'tile_size' => TileSize::Normal,
            'sort_order' => fake()->numberBetween(0, 20),
            'is_published' => false,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'is_published' => true,
        ]);
    }
}
