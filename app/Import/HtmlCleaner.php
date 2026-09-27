<?php

namespace App\Import;

/**
 * Normalises legacy TinyMCE/MS-Word HTML for the TipTap editor.
 */
class HtmlCleaner
{
	public int $eszett = 0;

	public function html(?string $html): ?string
	{
		if ($html === null || trim($html) === '') {
			return null;
		}

		$html = str_replace(['\\/', "\r\n", "\r"], ['/', "\n", "\n"], $html);

		// MS-Word conditional comments, all comments, <xml>/<style> blocks
		$html = preg_replace('/<!--\[if.*?<!\[endif\]-->/s', '', $html);
		$html = preg_replace('/<!--.*?-->/s', '', $html);
		$html = preg_replace('#<(xml|style)\b[^>]*>.*?</\1>#si', '', $html);

		// unwrap spans, drop presentational attributes
		$html = preg_replace('#</?span\b[^>]*>#i', '', $html);
		$html = preg_replace('/\s(class|style|lang)="[^"]*"/i', '', $html);

		$html = preg_replace('#<br\s*/?>#i', '<br>', $html);
		$html = $this->decodeEntities($html);
		$html = preg_replace('#<p>\s*</p>#', '', $html);

		$html = trim($html);

		return $html === '' ? null : $this->eszett($html);
	}

	/**
	 * Plain text value (titles, labels, spec lists).
	 */
	public function text(?string $value): ?string
	{
		if ($value === null) {
			return null;
		}

		$value = trim(html_entity_decode(str_replace('\\/', '/', $value), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

		return $value === '' ? null : $this->eszett($value);
	}

	/**
	 * Decode named/numeric entities except the ones that are HTML syntax.
	 */
	protected function decodeEntities(string $html): string
	{
		return preg_replace_callback('/&(#?[a-zA-Z0-9]+);/', function ($m) {
			if (in_array(strtolower($m[1]), ['lt', 'gt', 'amp', 'quot'], true)) {
				return $m[0];
			}

			return html_entity_decode($m[0], ENT_QUOTES | ENT_HTML5, 'UTF-8');
		}, $html);
	}

	/**
	 * Swiss orthography: ß → ss.
	 */
	protected function eszett(string $value): string
	{
		$this->eszett += substr_count($value, 'ß');

		return str_replace('ß', 'ss', $value);
	}
}
