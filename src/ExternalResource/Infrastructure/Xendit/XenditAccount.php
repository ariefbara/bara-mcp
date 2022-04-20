<?php

namespace ExternalResource\Infrastructure\Xendit;

use ExternalResource\Domain\Model\ExternalEntity;
use ExternalResource\Domain\Model\ExternalTask;
use Resources\Exception\RegularException;

class XenditAccount implements ExternalEntity
{

    protected function __construct()
    {
    }

    public static function withToken(string $token): self
    {
        if ($token !== env('XENDIT_CALLBACK_TOKEN')) {
            throw RegularException::forbidden('only xendit can make this request');
        }
        return new static();
    }

    public function executeExternalTask(ExternalTask $task, $payload): void
    {
        $task->execute($payload);
    }

}
