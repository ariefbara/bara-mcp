<?php

namespace Client\Domain\Model;

interface IClientTask
{

    public function execute(Client $client, $payload): void;
}
