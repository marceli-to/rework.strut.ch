<?php

namespace App\Http\Controllers\Api;

use App\Actions\Seo\UpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Seo\UpdateSeoSettingRequest;
use App\Http\Resources\SeoSettingResource;
use App\Models\SeoSetting;

class SeoController extends Controller
{
    public function show()
    {
        return new SeoSettingResource(SeoSetting::firstOrCreate([])->load('media'));
    }

    public function update(UpdateSeoSettingRequest $request)
    {
        $seo = SeoSetting::firstOrCreate([]);
        $seo = (new UpdateAction)->execute($seo, $request->validated());
        return new SeoSettingResource($seo->load('media'));
    }
}
