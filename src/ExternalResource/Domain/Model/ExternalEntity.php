<?php

namespace ExternalResource\Domain\Model;

interface ExternalEntity
{
    public function executeExternalTask(ExternalTask $task, $payload): void;
}
