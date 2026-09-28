<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

/**
 * Requests every legacy URL (docs/legacy-urls.txt) against the current data
 * and checks it answers 200, or 301 to a URL that answers 200.
 */
class CheckLegacyUrls extends Command
{
	protected $signature = 'strut:check-urls {--file=docs/legacy-urls.txt}';

	protected $description = 'Check that all legacy strut.ch URLs still work (200 or 301 → 200)';

	public function handle(Kernel $kernel): int
	{
		$lines = collect(file(base_path($this->option('file')), FILE_IGNORE_NEW_LINES))
			->reject(fn (string $line) => $line === '' || str_starts_with($line, '#'))
			->map(fn (string $line) => explode("\t", $line));

		$failed = [];
		$rows = $lines->map(function (array $line) use ($kernel, &$failed) {
			[$kind, $path] = $line;
			$status = $this->status($kernel, $path);
			$target = null;

			if ($status === 301) {
				$target = parse_url($this->location, PHP_URL_PATH) . (($q = parse_url($this->location, PHP_URL_QUERY)) ? "?$q" : '');
				$status = "301 → {$this->status($kernel, $target)}";
			}

			$ok = in_array($status, [200, '301 → 200'], true);
			if (!$ok) {
				$failed[] = $path;
			}

			return [$kind, $path, $status, $target, $ok ? '✓' : '✗'];
		});

		$this->table(['Art', 'URL', 'Status', 'Ziel', ''], $rows->all());
		$this->line($lines->count() . ' URLs, ' . count($failed) . ' fehlerhaft.');

		return $failed ? self::FAILURE : self::SUCCESS;
	}

	private ?string $location = null;

	private function status(Kernel $kernel, string $path): int
	{
		$response = $kernel->handle(Request::create($path));
		$this->location = $response->headers->get('Location');

		return $response->getStatusCode();
	}
}
