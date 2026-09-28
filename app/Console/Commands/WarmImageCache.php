<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Support\Glide;
use App\Support\ImageSupport;
use Illuminate\Console\Command;
use Throwable;

/**
 * Pre-generates the Glide cache for every image variant the public site
 * requests, so no visitor waits for a first AVIF/WebP encode.
 */
class WarmImageCache extends Command
{
	protected $signature = 'images:warm {--format=* : Only these formats (original, webp, avif)}';

	protected $description = 'Generate all public image variants (sizes × original/WebP/AVIF) into the Glide cache';

	/**
	 * Sizes the public views request, per owner type (see resources/views).
	 */
	private const SIZES = [
		'project' => ['sm', 'md', 'lg'],
		'page' => ['md', 'lg'],
		'entry' => ['xs'],
		'news' => ['xs'],
		'book' => ['sm'],
		'team_member' => ['sm'],
	];

	public function handle(): int
	{
		$formats = $this->option('format') ?: ['original', ...ImageSupport::modernFormats()];
		$server = Glide::server();
		$media = Media::where('collection', 'images')
			->where('mime_type', 'like', 'image/%')
			->whereIn('mediable_type', array_keys(self::SIZES))
			->get();

		$bar = $this->output->createProgressBar($media->sum(fn (Media $m) => count(self::SIZES[$m->mediable_type]) * count($formats)));
		$failed = 0;

		foreach ($media as $item) {
			foreach (self::SIZES[$item->mediable_type] as $size) {
				foreach ($formats as $format) {
					try {
						$server->makeImage($item->imagePath(), $item->imageParams($size, $format === 'original' ? null : $format));
					} catch (Throwable $e) {
						$failed++;
						$this->newLine();
						$this->warn("{$item->file} ({$size}, {$format}): {$e->getMessage()}");
					}
					$bar->advance();
				}
			}
		}

		$bar->finish();
		$this->newLine();
		$this->info("{$media->count()} images done" . ($failed ? ", {$failed} variants failed." : '.'));

		return $failed ? self::FAILURE : self::SUCCESS;
	}
}
