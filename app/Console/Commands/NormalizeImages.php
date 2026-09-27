<?php

namespace App\Console\Commands;

use App\Actions\Media\NormalizeAction;
use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class NormalizeImages extends Command
{
    protected $signature = 'images:normalize {--dry-run : Report what would change without writing}';
    protected $description = 'Downsize stored images whose longest edge exceeds the upload threshold';

    public function handle(): int
    {
        if (!extension_loaded('imagick')) {
            $this->error('Imagick extension is required.');
            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $normalize = new NormalizeAction;

        $scanned = 0;
        $shrunk = 0;
        $bytesSaved = 0;

        Media::query()->orderBy('id')->chunkById(100, function ($mediaItems) use ($dryRun, $normalize, &$scanned, &$shrunk, &$bytesSaved) {
            foreach ($mediaItems as $media) {
                $scanned++;

                if (!in_array($media->mime_type, NormalizeAction::NORMALIZABLE_MIMES, true)) {
                    continue;
                }

                $path = Storage::disk('public')->path('uploads/' . $media->file);
                if (!is_file($path)) {
                    continue;
                }

                $size = @getimagesize($path);
                if (!$size || max($size[0], $size[1]) <= NormalizeAction::MAX_SOURCE_EDGE) {
                    continue;
                }

                $before = filesize($path);
                $hasCrop = is_array($media->crop) && isset($media->crop['x'], $media->crop['y'], $media->crop['w'], $media->crop['h']);
                $this->line(sprintf('  %s — %d×%d (%s KB)%s',
                    $media->file, $size[0], $size[1], number_format($before / 1024),
                    $hasCrop ? ' (cropped)' : ''
                ));

                if ($dryRun) {
                    $shrunk++;
                    continue;
                }

                $scale = $normalize->execute($path, $media->mime_type);
                if ($scale === null || $scale >= 1.0) {
                    continue;
                }

                $after = @filesize($path) ?: $before;
                $newSize = @getimagesize($path);

                $crop = $media->crop;
                if (is_array($crop) && isset($crop['x'], $crop['y'], $crop['w'], $crop['h'])) {
                    $crop = [
                        'x' => (int) round($crop['x'] * $scale),
                        'y' => (int) round($crop['y'] * $scale),
                        'w' => (int) round($crop['w'] * $scale),
                        'h' => (int) round($crop['h'] * $scale),
                    ];
                }

                $media->update([
                    'width' => $newSize[0] ?? $media->width,
                    'height' => $newSize[1] ?? $media->height,
                    'size' => $after,
                    'crop' => $crop,
                ]);

                $bytesSaved += max(0, $before - $after);
                $shrunk++;
            }
        });

        $this->newLine();
        $this->info(sprintf('Scanned %d, %s %d. Saved ~%s KB.',
            $scanned,
            $dryRun ? 'would shrink' : 'shrunk',
            $shrunk,
            number_format($bytesSaved / 1024)
        ));

        if (!$dryRun && $shrunk > 0) {
            File::deleteDirectory(storage_path('app/.glide-cache'));
            $this->info('Glide cache cleared.');
        }

        return self::SUCCESS;
    }
}
