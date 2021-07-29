<?php

namespace Tests\src\Firm\Domain\Task\MeetingInitiator;

use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use PHPUnit\Framework\MockObject\MockObject;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class MeetingInitiatorTestBase extends TestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $meeting;
    
    /**
     * 
     * @var MockObject
     */
    protected $dispatcher;


    protected function setUp(): void
    {
        parent::setUp();
        $this->meeting = $this->buildMockOfClass(Meeting::class);
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
    }
}
