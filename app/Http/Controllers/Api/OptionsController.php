<?php

namespace App\Http\Controllers\Api;

use App\Enums\Competition;
use App\Enums\EntryType;
use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Project;

/**
 * Select options for admin forms.
 */
class OptionsController extends Controller
{
	public function __invoke()
	{
		return response()->json([
			'status' => ProjectStatus::options(),
			'competition' => Competition::options(),
			'entry_type' => EntryType::options(),
			'categories' => Category::ordered()->get()->map(fn (Category $category) => [
				'value' => $category->uuid,
				'label' => $category->name,
			]),
			'category_types' => Category::ordered()->with('types')->get()->flatMap(fn (Category $category) => $category->types->map(fn ($type) => [
				'value' => $type->uuid,
				'label' => $category->name . ' › ' . $type->name_singular,
			])),
			'projects' => Project::orderBy('name')->get()->map(fn (Project $project) => [
				'value' => $project->uuid,
				'label' => $project->full_title . ' (' . $project->year . ')',
			]),
		]);
	}
}
