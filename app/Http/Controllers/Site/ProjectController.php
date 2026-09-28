<?php

namespace App\Http\Controllers\Site;

use App\Actions\Site\GetProject;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Project detail (/bauten/{id}/{slug}). Unpublished projects are not found;
 * projects without a detail page are still reachable, as in legacy. Any other
 * slug redirects to the canonical URL.
 */
class ProjectController extends Controller
{
	public function __invoke(GetProject $action, Project $project, ?string $slug = null): View|RedirectResponse
	{
		abort_unless($project->publish, 404);

		if ($slug !== $project->slug) {
			return redirect($project->url, 301);
		}

		return view('pages.project', $action->execute($project));
	}
}
