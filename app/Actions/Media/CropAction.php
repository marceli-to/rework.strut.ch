<?php

namespace App\Actions\Media;

use App\Models\Media;

class CropAction
{
    public function execute(Media $media, array $data): Media
    {
        $allNull = is_null($data['x']) && is_null($data['y'])
            && is_null($data['w']) && is_null($data['h']);

        $media->update([
            'crop' => $allNull ? null : [
                'x' => $data['x'],
                'y' => $data['y'],
                'w' => $data['w'],
                'h' => $data['h'],
            ],
        ]);

        return $media;
    }
}
