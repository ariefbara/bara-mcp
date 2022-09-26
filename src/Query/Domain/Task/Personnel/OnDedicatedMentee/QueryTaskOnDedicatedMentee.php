<?php

namespace Query\Domain\Task\Personnel\OnDedicatedMentee;

interface QueryTaskOnDedicatedMentee
{

    public function execute(string $menteeId, $payload): void;
}
