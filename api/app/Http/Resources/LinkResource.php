<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin array{code: string, url: string, clicks: int, created_at: string} */
class LinkResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->resource['code'],
            'url' => $this->resource['url'],
            'short_url' => url($this->resource['code']),
            'created_at' => $this->resource['created_at'],
        ];
    }
}
