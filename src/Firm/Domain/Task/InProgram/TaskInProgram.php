<?php

namespace Firm\Domain\Task\InProgram;

use Firm\Domain\Model\Firm\Program;

interface TaskInProgram
{

    public function execute(Program $program, $payload): void;
}
