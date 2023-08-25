<?php

namespace GurmesoftSms;

use Exception;

class Client
{
    public function __construct(string $provider, array $options)
    {
        $class = "\\GurmesoftSms\\Providers\\$provider";
        $this->class = new $class($options);
    }

    public function send(string $message, array $numbers, int|null|bool $filter = null, null|int $appKey)
    {
        return $this->class->send($message, $numbers, $filter, $appKey);
    }

    public function addDirectory(string $firstName, string $lastName, string $phone, string $group)
    {
        return $this->class->addDirectory($firstName, $lastName, $phone, $group);
    }

    public function info(string $id)
    {
        return $this->class->info($id);
    }

    public function checkCredit()
    {
        return $this->class->checkCredit();
    }

    private function empty($param)
    {
        if (empty($param)) {
            throw new Exception(__CLASS__ . " exception message cannot be empty.");
        }
    }
}
