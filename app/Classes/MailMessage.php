<?php

namespace App\Classes;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Notifications\Messages\MailMessage as BaseMailMessage;

class MailMessage extends BaseMailMessage implements Renderable
{
    public function withData(array $data = [])
    {
        $this->viewData = array_merge($this->viewData, $data);
        return $this;
    }
}