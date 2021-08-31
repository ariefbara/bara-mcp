<?php

namespace Query\Domain\Model\Firm;

interface ITaskExecutableByClient
{
    public function execute(string $clientId): void;
}
