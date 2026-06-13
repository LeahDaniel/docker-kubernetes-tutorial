<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLinkRequest;
use App\Http\Resources\LinkResource;
use App\Services\InMemoryLinkStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class LinkController extends Controller
{
    public function __construct(private InMemoryLinkStore $store) {}

    public function store(StoreLinkRequest $request): JsonResponse
    {
        $link = $this->store->create($request->validated('url'));

        return LinkResource::make($link)
            ->response()
            ->setStatusCode(201);
    }

    public function redirect(string $code): RedirectResponse|JsonResponse
    {
        $link = $this->store->find($code);

        if ($link === null) {
            return response()->json(['message' => 'Link not found.'], 404);
        }

        $this->store->recordClick($code);

        return redirect()->away($link['url']);
    }
}
