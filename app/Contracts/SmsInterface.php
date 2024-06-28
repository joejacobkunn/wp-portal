<?php
namespace App\Contracts;

interface SmsInterface
{
    public function create($body);
    public function getUser($params);

    public function getMessages($params);

    public function send($body);
}
