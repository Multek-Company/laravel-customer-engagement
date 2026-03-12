<?php

namespace Multek\CustomerEngagement\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncCustomer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [10, 60, 300];

    public function __construct(
        public $user,
        public ?string $driver = null,
    ) {
        $this->onQueue(config('customer-engagement.queue', 'default'));
    }

    public function handle(): void
    {
        $this->user->syncToEngagement($this->driver);
    }
}
