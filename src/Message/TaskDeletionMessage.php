<?php

namespace App\Message;

class TaskDeletionMessage
{
    private \DateTimeInterface $olderThan;

    public function __construct(\DateTimeInterface $olderThan)
    {
        $this->olderThan = $olderThan;
    }

    public function getOlderThan(): \DateTimeInterface
    {
        return $this->olderThan;
    }
}