<?php

namespace Tests\src\Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program;
use Tests\TestBase;

class TaskInProgramTestBase extends TestBase
{
    protected $program;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
    }
}
