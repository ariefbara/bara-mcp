<?php

namespace Tests\src\Participant\Domain\Task\Participant;

use Participant\Domain\Model\Participant\BookedMentoringSlot;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\BookedMentoringSlotRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\src\Participant\Domain\Task\Participant\TaskExecutableByParticipantTestBase;

class BookedMentoringSlotTaskTestBase extends TaskExecutableByParticipantTestBase
{

    /**
     * 
     * @var MockObject
     */
    protected $bookedMentoringSlotRepository;

    /**
     * 
     * @var MockObject
     */
    protected $bookedMentoringSlot;
    protected $bookedMentoringSlotId = 'bookedMentoringSlotId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookedMentoringSlot = $this->buildMockOfClass(BookedMentoringSlot::class);
        $this->bookedMentoringSlotRepository = $this->buildMockOfInterface(BookedMentoringSlotRepository::class);
        $this->bookedMentoringSlotRepository->expects($this->any())
                ->method('ofId')
                ->with($this->bookedMentoringSlotId)
                ->willReturn($this->bookedMentoringSlot);
    }

}
