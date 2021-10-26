<?php

namespace Tests\src\Participant\Domain\Task\Participant;

use Participant\Domain\Model\Participant;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class TaskExecutableByParticipantTestBase extends TestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $participant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
    }
}
