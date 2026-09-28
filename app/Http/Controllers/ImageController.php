<?php

namespace App\Http\Controllers;

use App\Support\Glide;
use App\Support\ImageSupport;
use App\Support\OgImage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Glide\Server;

class ImageController extends Controller
{
	protected Server $server;

	public function __construct()
	{
		$this->server = Glide::server();
	}

	public function show(Request $request, string $path): Response
	{
		$params = $request->all();
		if (isset($params['fm']) && !ImageSupport::supports((string) $params['fm'])) {
			unset($params['fm']);
		}

		return $this->respond($path, $params);
	}

	public function og(string $file): Response
	{
		return $this->respond('uploads/' . $file, [
			'w' => OgImage::WIDTH,
			'h' => OgImage::HEIGHT,
			'fit' => 'crop',
			'fm' => 'jpg',
			'q' => 85,
		]);
	}

	protected function respond(string $path, array $params): Response
	{
		$cachedPath = $this->server->makeImage($path, $params);
		$imageContent = $this->server->getCache()->read($cachedPath);
		$mimeType = $this->resolveMimeType($path, $params);

		return response($imageContent, 200)
			->header('Content-Type', $mimeType)
			->header('Cache-Control', 'max-age=31536000, public')
			->header('Expires', now()->addYear()->toRfc7231String());
	}

	protected function resolveMimeType(string $path, array $params): string
	{
		$format = strtolower((string) ($params['fm'] ?? pathinfo($path, PATHINFO_EXTENSION)));

		return match ($format) {
			'avif' => 'image/avif',
			'webp' => 'image/webp',
			'png' => 'image/png',
			'gif' => 'image/gif',
			default => 'image/jpeg',
		};
	}
}
