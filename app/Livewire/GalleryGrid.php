<?php

namespace App\Livewire;

use App\Enums\ProjectCategory;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class GalleryGrid extends Component
{
    public string $filter = 'all';

    public ?int $viewingId = null;

    public int $photoIndex = 0;

    public int $number = 3;

    public function mount(?string $category = null): void
    {
        $requested = $category ?? request()->query('category');

        if (is_string($requested) && $requested !== '') {
            $this->setFilter($requested);
        }
    }

    public function setFilter(string $filter): void
    {
        $allowed = $this->publishedProjects()
            ->pluck('category')
            ->map(fn (ProjectCategory $category): string => $category->value);

        $this->filter = $filter === 'all' || $allowed->contains($filter) ? $filter : 'all';
        $this->close();
    }

    public function open(int $projectId): void
    {
        $project = Project::query()->published()->find($projectId);

        if ($project === null) {
            return;
        }

        $this->viewingId = $project->id;
        $this->photoIndex = 0;
    }

    public function close(): void
    {
        $this->viewingId = null;
        $this->photoIndex = 0;
    }

    public function next(): void
    {
        $count = $this->photoCount();

        if ($count < 2) {
            return;
        }

        $this->photoIndex = ($this->photoIndex + 1) % $count;
    }

    public function previous(): void
    {
        $count = $this->photoCount();

        if ($count < 2) {
            return;
        }

        $this->photoIndex = ($this->photoIndex - 1 + $count) % $count;
    }

    public function render(): View
    {
        $projects = $this->publishedProjects();
        $categories = collect(ProjectCategory::cases())
            ->filter(fn (ProjectCategory $category): bool => $projects->contains(
                fn (Project $project): bool => $project->category === $category,
            ))
            ->values();

        $viewing = $projects->firstWhere('id', $this->viewingId);
        $photos = $viewing?->getMedia('photos') ?? new Collection;
        $index = $photos->isEmpty() ? 0 : $this->photoIndex % $photos->count();

        return view('livewire.gallery-grid', [
            'projects' => $projects,
            'categories' => $categories,
            'viewing' => $viewing,
            'photos' => $photos,
            'photo' => $photos->get($index),
        ]);
    }

    /**
     * @return Collection<int, Project>
     */
    private function publishedProjects(): Collection
    {
        return Project::query()
            ->published()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with('media')
            ->get();
    }

    private function photoCount(): int
    {
        $project = $this->viewingId === null
            ? null
            : Project::query()->published()->with('media')->find($this->viewingId);

        return $project?->getMedia('photos')->count() ?? 0;
    }
}
