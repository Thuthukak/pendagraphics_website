<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'layouts.app';

    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $shared = [
            'auth' => [
                'user' => $request->user(),
            ],
            // Add global meta data that will be available on all pages
            'globalMeta' => [
                'site_name' => config('app.name', 'Penda Graphics'),
                'default_image' => asset('assets/images/penda_logo2.png'),
                'site_url' => config('app.url'),
            ],
        ];

        // Only add Ziggy if the class exists
        if (class_exists(Ziggy::class)) {
            $shared['ziggy'] = function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            };
        }

        return array_merge(parent::share($request), $shared);
    }
}