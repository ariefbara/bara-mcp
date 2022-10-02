<?php

namespace Query\Domain\Task\Personnel;

interface PersonnelTask
{

    public function execute(string $personnelId, $payload): void;
}
