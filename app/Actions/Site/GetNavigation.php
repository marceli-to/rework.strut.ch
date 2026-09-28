<?php

namespace App\Actions\Site;

use App\Models\Category;
use App\Models\Project;

/**
 * Main navigation: "Bauten" (published categories → published types → published
 * projects with a detail page) plus the fixed page links.
 *
 * Active states follow the legacy site, including its quirk that "Werkliste" is
 * only active on /werkliste itself, not on its sub-views.
 */
class GetNavigation
{
	public function execute(?string $route, ?Project $project = null): array
	{
		$section = fn (string $label, array $links) => [
			'label' => $label,
			'active' => in_array($route, array_column($links, 'route'), true),
			'links' => array_map(fn ($link) => $link + ['active' => $link['route'] === $route], $links),
		];

		return [
			'projects' => [
				'label' => 'Bauten',
				'active' => $route === 'page.project',
				'categories' => $this->categories($project),
			],
			'works' => ['label' => 'Werkliste', 'route' => 'page.works', 'active' => $route === 'page.works'],
			'publications' => $section('Publikationen', [
				['label' => 'Presse', 'route' => 'page.press'],
				['label' => 'Bücher', 'route' => 'page.books'],
				['label' => 'Downloads', 'route' => 'page.downloads'],
			]),
			'about' => $section('Büro', [
				['label' => 'Über uns', 'route' => 'page.about'],
				['label' => 'Jobs', 'route' => 'page.jobs'],
				['label' => 'Auszeichnungen', 'route' => 'page.awards'],
				['label' => 'Vorträge', 'route' => 'page.lectures'],
			]),
			'contact' => ['label' => 'Kontakt', 'route' => 'page.contact', 'active' => $route === 'page.contact'],
		];
	}

	private function categories(?Project $current): array
	{
		$typeId = $current?->category_type_id;
		$categoryId = $current?->categoryType?->category_id;

		return Category::published()
			->orderBy('sort_order')->orderBy('id')
			->with(['types' => fn ($q) => $q->published()->with(['projects' => fn ($q) => $q->published()->detailed()])])
			->get()
			->map(fn (Category $category) => [
				'label' => $category->name,
				'active' => $category->id === $categoryId,
				'show_types' => $category->show_types,
				'types' => $category->types
					->filter(fn ($type) => $type->projects->isNotEmpty())
					->map(fn ($type) => [
						'label' => $type->name_plural,
						'active' => $type->id === $typeId,
						'projects' => $type->projects->map(fn (Project $project) => [
							'label' => $project->full_title,
							'url' => $project->url,
							'active' => $project->id === $current?->id,
						])->all(),
					])->values()->all(),
			])->all();
	}
}
