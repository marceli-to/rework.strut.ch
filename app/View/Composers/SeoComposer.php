<?php

namespace App\View\Composers;

use App\Models\SeoSetting;
use Illuminate\View\View;

class SeoComposer
{
    public function compose(View $view): void
    {
        $view->with('seo', SeoSetting::with('media')->first());
    }
}
