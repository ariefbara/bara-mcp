<?php

namespace Query\Domain\Task\InProgram;

interface ProgramTask
{

    public function execute(string $programId, $payload): void;
}
