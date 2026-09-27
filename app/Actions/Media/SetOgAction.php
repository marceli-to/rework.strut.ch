<?php

namespace App\Actions\Media;

use App\Models\Media;

class SetOgAction
{
    public function execute(Media $media): Media
    {
        $wasOg = $media->is_og;

        Media::where('mediable_type', $media->mediable_type)
            ->where('mediable_id', $media->mediable_id)
            ->update(['is_og' => false]);

        if (!$wasOg) {
            $media->update(['is_og' => true]);
        }

        return $media->refresh();
    }
}
