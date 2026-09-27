<?php

namespace App\View\Components\Media;

use App\Models\Media;
use App\Support\ImageSupport;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class Image extends Component
{
    public string $src;
    public string $alt;
    public int $width;
    public int $height;
    public ?array $crop;
    public float $aspectRatio;
    public string $fit;
    public int $quality;
    public array $formats;
    public string $class;
    public string $loading;
    public string $sizes;
    public array $sources = [];
    public array $mobileSources = [];
    public string $fallbackUrl;
    public bool $hasMobileVariant;

    protected const WIDTHS = [480, 640, 768, 1024, 1280, 1440, 1600, 1920];

    public function __construct(
        Media|Collection $media,
        string $sizes = '100vw',
        int $maxWidth = 1920,
        ?string $alt = null,
        string $fit = 'crop',
        int $quality = 90,
        array $formats = ['avif', 'webp', 'jpg'],
        string $class = '',
        string $loading = 'lazy',
    ) {
        if ($media instanceof Collection) {
            $desktopMedia = $media->where('variant', 'desktop')->first();
            $mobileMedia = $media->where('variant', 'mobile')->first();

            // Fall back: if only one variant exists, use it as the primary
            if (!$desktopMedia && $mobileMedia) {
                $desktopMedia = $mobileMedia;
                $mobileMedia = null;
            } elseif (!$desktopMedia) {
                $desktopMedia = $media->first();
                $mobileMedia = null;
            }
        } else {
            $desktopMedia = $media;
            $mobileMedia = null;
        }

        if (!$desktopMedia) {
            throw new \InvalidArgumentException('Image component requires at least one media item.');
        }

        $this->src = 'uploads/' . $desktopMedia->file;
        $this->alt = $alt ?? $desktopMedia->alt ?? '';
        $this->crop = $desktopMedia->crop;
        $this->fit = $fit;
        $this->quality = $quality;
        $this->formats = array_values(array_filter($formats, fn ($f) => ImageSupport::supports($f)));
        $this->class = $class;
        $this->loading = $loading;
        $this->sizes = $sizes;
        $this->hasMobileVariant = $mobileMedia !== null;

        if ($this->fit === 'crop' && $this->crop && isset($this->crop['w'], $this->crop['h'])) {
            $this->aspectRatio = $this->crop['h'] / $this->crop['w'];
        } else {
            $baseWidth = $desktopMedia->width ?? 1;
            $baseHeight = $desktopMedia->height ?? 1;
            $this->aspectRatio = $baseHeight / $baseWidth;
        }

        $widths = array_values(array_filter(self::WIDTHS, fn ($w) => $w <= $maxWidth));
        if (empty($widths)) {
            $widths = [$maxWidth];
        }
        $this->width = end($widths);
        $this->height = (int) round($this->width * $this->aspectRatio);

        if ($desktopMedia->mime_type === 'image/gif') {
            $this->fallbackUrl = '/storage/uploads/' . $desktopMedia->file;
            return;
        }

        $this->buildSources($widths, $mobileMedia);
    }

    protected function buildSources(array $widths, ?Media $mobileMedia): void
    {
        if ($mobileMedia) {
            $mobileSrc = 'uploads/' . $mobileMedia->file;
            $mobileCrop = $mobileMedia->crop;

            if ($this->fit === 'crop' && $mobileCrop && isset($mobileCrop['w'], $mobileCrop['h'])) {
                $mobileAspectRatio = $mobileCrop['h'] / $mobileCrop['w'];
            } else {
                $mobileAspectRatio = ($mobileMedia->height ?? 1) / ($mobileMedia->width ?? 1);
            }

            $mobileWidths = array_values(array_filter($widths, fn ($w) => $w <= 768));

            foreach ($this->formats as $format) {
                if ($format === 'jpg' || $format === 'jpeg') {
                    continue;
                }
                $this->mobileSources[] = [
                    'srcset' => $this->buildSrcsetFor($format, $mobileWidths, $mobileSrc, $mobileCrop, $mobileAspectRatio),
                    'type' => $this->getMimeType($format),
                    'sizes' => $this->sizes,
                    'media' => '(max-width: 767px)',
                ];
            }
        }

        foreach ($this->formats as $format) {
            if ($format === 'jpg' || $format === 'jpeg') {
                continue;
            }

            $source = [
                'srcset' => $this->buildSrcset($format, $widths),
                'type' => $this->getMimeType($format),
                'sizes' => $this->sizes,
            ];

            if ($this->hasMobileVariant) {
                $source['media'] = '(min-width: 768px)';
            }

            $this->sources[] = $source;
        }

        $this->fallbackUrl = $this->buildUrl('jpg', $this->width, $this->height);
    }

    protected function buildSrcset(string $format, array $widths): string
    {
        $parts = [];
        foreach ($widths as $w) {
            $h = (int) round($w * $this->aspectRatio);
            $parts[] = $this->buildUrl($format, $w, $h) . ' ' . $w . 'w';
        }

        return implode(', ', $parts);
    }

    protected function buildSrcsetFor(string $format, array $widths, string $src, ?array $crop, float $aspectRatio): string
    {
        $parts = [];
        foreach ($widths as $w) {
            $h = (int) round($w * $aspectRatio);
            $parts[] = $this->buildUrlFor($format, $w, $h, $src, $crop) . ' ' . $w . 'w';
        }

        return implode(', ', $parts);
    }

    protected function buildUrl(string $format, int $width, int $height): string
    {
        return $this->buildUrlFor($format, $width, $height, $this->src, $this->crop);
    }

    protected function buildUrlFor(string $format, int $width, int $height, string $src, ?array $crop): string
    {
        $params = [
            'w=' . $width,
            'h=' . $height,
            'fit=' . $this->fit,
        ];

        if ($crop && $this->fit === 'crop' && isset($crop['w'], $crop['h'], $crop['x'], $crop['y'])) {
            $params[] = 'crop=' . $crop['w'] . ',' . $crop['h'] . ',' . $crop['x'] . ',' . $crop['y'];
        }

        $params[] = 'fm=' . $format;
        $params[] = 'q=' . $this->quality;

        return '/img/' . $src . '?' . implode('&', $params);
    }

    protected function getMimeType(string $format): string
    {
        return match ($format) {
            'avif' => 'image/avif',
            'webp' => 'image/webp',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            default => 'image/jpeg',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.media.image');
    }
}
