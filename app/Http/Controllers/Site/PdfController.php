<?php

namespace App\Http\Controllers\Site;

use App\Actions\Site\GetWorksPdf;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\LegacyMap;
use App\Models\Media;
use App\Models\Project;
use App\Support\Pdf\PdfMerger;
use App\Support\Pdf\PdfRenderer;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Werkliste PDFs (8 variants) and the merged project documentation per
 * category, as the legacy PdfController (Q7: kept 1:1).
 */
class PdfController extends Controller
{
	public function works(GetWorksPdf $action, PdfRenderer $renderer, string $variant): Response
	{
		$data = $action->execute($variant) + ['date' => now()->locale('de')->translatedFormat('d. F Y')];

		$pdf = $renderer->render('pdf.works', $data, function (Dompdf $dompdf) {
			// Page number bottom right (legacy: inline PHP script with page_text).
			$font = $dompdf->getFontMetrics()->getFont('basis-grotesque-regular-pro', 'normal');
			$dompdf->getCanvas()->page_text(543, 810, '{PAGE_NUM}/{PAGE_COUNT}', $font, 9.5, [0, 0, 0]);
		});

		return $this->inline($pdf, 'strut.ch-werkliste-' . GetWorksPdf::VARIANTS[$variant]['file'] . '-' . now()->format('d.m.Y') . '.pdf');
	}

	/**
	 * All PDF downloads of the category's published projects in one file.
	 */
	public function category(PdfMerger $merger, int $category, ?string $slug = null): Response|RedirectResponse
	{
		// Legacy links use the old category ids (1–3); they redirect to the new ones.
		$found = Category::find($category);
		if (!$found) {
			$id = LegacyMap::where('legacy_table', 'categories')->where('legacy_id', $category)->value('model_id');
			abort_unless($id, 404);

			return redirect()->route('pdf.category', array_filter([$id, $slug]), 301);
		}
		$category = $found;

		$paths = Project::published()
			->whereHas('categoryType', fn ($q) => $q->where('category_id', $category->id))
			->with('files')
			->orderBy('id')
			->get()
			->flatMap(fn (Project $project) => $project->files->map(fn (Media $file) => storage_path('app/public/' . $file->imagePath())))
			->filter(fn (string $path) => is_file($path))
			->values()
			->all();

		abort_if($paths === [], 404);

		$name = 'strut.ch-Projektdokumentation-' . Str::ucfirst($slug ?: Str::slug($category->name)) . '-' . now()->format('d-m-Y:H:i:s') . '.pdf';

		// Merging takes seconds; the result only changes with the source files.
		$key = md5(collect($paths)->map(fn (string $path) => $path . '@' . filemtime($path))->implode('|'));
		$cached = storage_path("app/pdf-cache/category-{$category->id}-{$key}.pdf");

		if (!is_file($cached)) {
			File::ensureDirectoryExists(dirname($cached));
			File::delete(File::glob(storage_path("app/pdf-cache/category-{$category->id}-*.pdf")));
			File::put($cached, $merger->merge($paths));
		}

		return $this->inline(File::get($cached), $name);
	}

	private function inline(string $pdf, string $filename): Response
	{
		return response($pdf, 200, [
			'Content-Type' => 'application/pdf',
			'Content-Disposition' => 'inline; filename="' . $filename . '"',
		]);
	}
}
