<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLinkRequest;
use App\Http\Resources\LinkResource;
use App\Jobs\RecordClick;
use App\Models\Link;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class LinkController extends Controller
{
    private const CACHE_TTL_SECONDS = 86400;

    public function store(StoreLinkRequest $request): JsonResponse
    {
        do {
            $code = Str::lower(Str::random(6));
        } while (Link::where('code', $code)->exists());

        $link = Link::create([
            'code' => $code,
            'url' => $request->validated('url'),
        ]);

        Cache::put($this->cacheKey($code), $link->url, self::CACHE_TTL_SECONDS);

        return LinkResource::make($link)
            ->response()
            ->setStatusCode(201);
    }

    public function redirect(string $code): RedirectResponse|JsonResponse
    {
        $url = Cache::get($this->cacheKey($code));

        if ($url === null) {
            $link = Link::where('code', $code)->first();

            if ($link === null) {
                return response()->json(['message' => 'Link not found.'], 404);
            }

            $url = $link->url;
            Cache::put($this->cacheKey($code), $url, self::CACHE_TTL_SECONDS);
        }

        RecordClick::dispatch($code);

        return redirect()->away($url);
    }

    private function cacheKey(string $code): string
    {
        return "link:{$code}";
    }
}
