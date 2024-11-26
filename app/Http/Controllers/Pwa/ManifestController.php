<?php

namespace App\Http\Controllers\Pwa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManifestController extends Controller
{
    public function manifest()
    {
        $basicManifest =  [
            'name' => config('pwa.fortis.manifest.name'),
            'short_name' => config('pwa.fortis.manifest.short_name'),
            'start_url' => route('pwa.index'),
            'display' => config('pwa.fortis.manifest.display'),
            'theme_color' => config('pwa.fortis.manifest.theme_color'),
            'background_color' => config('pwa.fortis.manifest.background_color'),
            'orientation' =>  config('pwa.fortis.manifest.orientation'),
        ];

        $basicManifest['icons'] = config('pwa.fortis.manifest.icons');

        return response()
            ->json($basicManifest)
            ->header('Content-Type', 'application/json')
            ->header('Cache-Control', 'max-age=86400, public');
    }
}
