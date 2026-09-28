<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\LegacyMap;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;

/**
 * 301 redirects for legacy URLs that have no page of their own (Q10, Q11).
 */
class LegacyRedirectController extends Controller
{
	/**
	 * Legacy size folders (storage/media/{folder}) and /media/{file}/{size} sizes → Media::SIZES.
	 * thumbs and grid were admin-only sizes.
	 */
	private const SIZES = [
		'xsmall' => 'xs', 'small' => 'sm', 'medium' => 'md', 'large' => 'lg', 'thumbs' => 'xs', 'grid' => 'md',
		'xs' => 'xs', 'sm' => 'sm', 'md' => 'md', 'lg' => 'lg',
	];

	/**
	 * /storage/media/{file}, /storage/media/{folder}/{file} (folder: a size or "downloads").
	 */
	public function storage(string $path): RedirectResponse
	{
		$parts = explode('/', $path);
		$file = array_pop($parts);
		$folder = $parts[0] ?? null;

		return $this->to($file, self::SIZES[$folder] ?? null);
	}

	/**
	 * /media/{file}/{size?} (legacy on-demand resizing).
	 */
	public function media(string $file, ?string $size = null): RedirectResponse
	{
		return $this->to($file, self::SIZES[$size] ?? 'sm');
	}

	private function to(string $legacyFile, ?string $size): RedirectResponse
	{
		$id = LegacyMap::where('model_type', 'media')->where('legacy_file', $legacyFile)->value('model_id');
		$media = $id ? Media::find($id) : null;
		abort_unless($media, 404);

		$url = $media->isImage() && $size ? $media->imageUrl($size) : $media->url();

		return redirect($url, 301);
	}
}
