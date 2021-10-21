<?php

namespace Tests\src\Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class MentorTaskTestBase extends TestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $mentor;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->mentor = $this->buildMockOfClass(ProgramConsultant::class);
    }
}
