<?php

namespace App\Jobs;

use App\Services\SXSync;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob as SpatieProcessWebhookJob;

class ProcessSXWebhookJob extends SpatieProcessWebhookJob
{
    public function handle(SXSync $sx_sync)
    {
        // $this->webhookCall // contains an instance of `WebhookCall`
        $sx_sync->sync($this->webhookCall);

    }
}