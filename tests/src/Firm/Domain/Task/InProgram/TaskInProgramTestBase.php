<?php

namespace Tests\src\Firm\Domain\Task\InProgram;

use Firm\Domain\Model\Firm\Program;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class TaskInProgramTestBase extends TestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $program;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
    }
}
