<?php

namespace Tests\src\Personnel\Domain\Task\DedicatedMentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DedicatedMentor;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class TaskExecutableByDedicatedMentorTestBase extends TestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $dedicatedMentor;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->dedicatedMentor = $this->buildMockOfClass(DedicatedMentor::class);
    }
}
