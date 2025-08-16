<?php

namespace App\Jobs;

use App\Services\AiPostGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class GenerateHourlyPostJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?string $title;

    public function __construct(?string $title = null)
    {
        $this->title = $title;
    }

    public function handle(AiPostGenerator $generator): void
    {

        $generator->createPostFromTitle();
    }
}
