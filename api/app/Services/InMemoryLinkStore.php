<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Temporary link storage for Phase 1 (no database server yet).
 *
 * PHP resets state between HTTP requests in dev, so we persist to a local
 * JSON file. Postgres replaces this in Phase 2.
 */
class InMemoryLinkStore
{
    /** @var array<string, array{code: string, url: string, clicks: int, created_at: string}>|null */
    private static ?array $links = null;

    private function path(): string
    {
        return storage_path('framework/links.json');
    }

    /** @return array<string, array{code: string, url: string, clicks: int, created_at: string}> */
    private function allLinks(): array
    {
        if (self::$links !== null) {
            return self::$links;
        }

        $path = $this->path();

        if (! is_file($path)) {
            return self::$links = [];
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        return self::$links = is_array($decoded) ? $decoded : [];
    }

    /** @param array<string, array{code: string, url: string, clicks: int, created_at: string}> $links */
    private function save(array $links): void
    {
        self::$links = $links;

        file_put_contents(
            $this->path(),
            json_encode($links, JSON_PRETTY_PRINT),
            LOCK_EX,
        );
    }

    public function create(string $url): array
    {
        $links = $this->allLinks();

        do {
            $code = Str::lower(Str::random(6));
        } while (isset($links[$code]));

        $link = [
            'code' => $code,
            'url' => $url,
            'clicks' => 0,
            'created_at' => now()->toIso8601String(),
        ];

        $links[$code] = $link;
        $this->save($links);

        return $link;
    }

    public function find(string $code): ?array
    {
        $links = $this->allLinks();

        return $links[$code] ?? null;
    }

    public function recordClick(string $code): ?array
    {
        $links = $this->allLinks();

        if (! isset($links[$code])) {
            return null;
        }

        $links[$code]['clicks']++;
        $this->save($links);

        return $links[$code];
    }
}
