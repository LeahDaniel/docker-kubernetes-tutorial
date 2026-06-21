<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLinkRequest;
use App\Http\Resources\LinkResource;
use App\Models\Link;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class LinkController extends Controller
{
    public function store(StoreLinkRequest $request): JsonResponse
    {
        do {
            $code = Str::lower(Str::random(6));
        } while (Link::where('code', $code)->exists());

        $link = Link::create([
            'code' => $code,
            'url' => $request->validated('url'),
        ]);

        return LinkResource::make($link)
            ->response()
            ->setStatusCode(201);
    }

    public function redirect(string $code): RedirectResponse|JsonResponse
    {
        $link = Link::where('code', $code)->first();

        if ($link === null) {
            return response()->json(['message' => 'Link not found.'], 404);
        }

        $link->increment('clicks');

        return redirect()->away($link->url);
    }
}
