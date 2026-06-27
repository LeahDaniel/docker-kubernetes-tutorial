<?php

namespace App\Jobs;

use App\Models\Link;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RecordClick implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $code) {}

    public function handle(): void
    {
        Link::where('code', $this->code)->increment('clicks');
    }
}
