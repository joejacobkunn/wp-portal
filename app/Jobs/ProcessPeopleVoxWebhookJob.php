<?php

namespace App\Jobs;

use App\Services\PeopleVox;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

class ProcessPeopleVoxWebhookJob extends ProcessWebhookJob
{

    /**
     * Execute the job.
     */
    public function handle(PeopleVox $peopleVox): void
    {
        $peopleVox->sync($this->webhookCall);
    }
}
