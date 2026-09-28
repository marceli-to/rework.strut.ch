<?php

namespace App\Support\Pdf;

use TCPDI;

/**
 * Merges PDF files page by page (TCPDI, which also reads PDF 1.5+ with
 * compressed object streams), keeping each page's size and orientation.
 */
class PdfMerger
{
	/**
	 * @param array<int, string> $paths absolute file paths
	 */
	public function merge(array $paths): string
	{
		$pdf = new TCPDI();
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		foreach ($paths as $path) {
			$pages = $pdf->setSourceFile($path);
			for ($page = 1; $page <= $pages; $page++) {
				$template = $pdf->importPage($page);
				$size = $pdf->getTemplateSize($template);
				$pdf->AddPage($size['w'] > $size['h'] ? 'L' : 'P', [$size['w'], $size['h']]);
				$pdf->useTemplate($template);
			}
		}

		return $pdf->Output('', 'S');
	}
}
