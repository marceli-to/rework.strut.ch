<?php

namespace App\Actions\Seo;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\SeoSetting;

class UpdateAction
{
    public function execute(SeoSetting $seo, array $data): SeoSetting
    {
        $media = $data['media'] ?? [];
        unset($data['media']);

        $seo->update($data);

        if (!empty($media)) {
            (new AttachMediaAction)->execute($media, $seo);
        }

        return $seo;
    }
}
