<?php

namespace App\Support\Pdf;

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Renders a Blade view to PDF with dompdf, with the legacy options
 * (A4, 96 dpi, font height ratio 1.1). Fonts and the logo are local files
 * in resources/pdf; remote loading and inline PHP stay off.
 */
class PdfRenderer
{
	public function render(string $view, array $data, ?callable $afterRender = null): string
	{
		$options = new Options([
			'chroot' => resource_path('pdf'),
			'fontDir' => storage_path('fonts'),
			'fontCache' => storage_path('fonts'),
			'tempDir' => sys_get_temp_dir(),
			'defaultMediaType' => 'screen',
			'defaultPaperSize' => 'a4',
			'defaultFont' => 'serif',
			'dpi' => 96,
			'fontHeightRatio' => 1.1,
			'isRemoteEnabled' => false,
			'isPhpEnabled' => false,
			'isFontSubsettingEnabled' => true,
		]);

		$dompdf = new Dompdf($options);
		$dompdf->loadHtml(view($view, $data)->render());
		$dompdf->setPaper('A4');
		$dompdf->render();

		if ($afterRender) {
			$afterRender($dompdf);
		}

		return $dompdf->output();
	}
}
