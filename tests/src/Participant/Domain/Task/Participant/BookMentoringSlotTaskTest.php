<?php

namespace Participant\Domain\Task\Dependency\Firm\Program\Participant;

use Participant\Domain\DependencyModel\Firm\Program\Consultant\MentoringSlot;
use Participant\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlotRepository;
use Participant\Domain\Task\Participant\BookMentoringSlotPayload;
use Participant\Domain\Task\Participant\BookMentoringSlotTask;
use Tests\src\Participant\Domain\Task\Participant\BookedMentoringSlotTaskTestBase;

class BookMentoringSlotTaskTest extends BookedMentoringSlotTaskTestBase
{
    protected $mentoringSlotRepository;
    protected $payload;
    protected $task;
    
    protected $mentoringSlot;
    protected $mentoringSlotId = 'mentoringSlotId';


    protected function setUp(): void
    {
        parent::setUp();
        $this->mentoringSlotRepository = $this->buildMockOfInterface(MentoringSlotRepository::class);
        $this->payload = new BookMentoringSlotPayload($this->mentoringSlotId);
        $this->task = new BookMentoringSlotTask(
                $this->bookedMentoringSlotRepository, $this->mentoringSlotRepository, $this->payload);
        
        $this->mentoringSlot = $this->buildMockOfClass(MentoringSlot::class);
        $this->mentoringSlotRepository->expects($this->any())
                ->method('ofId')
                ->with($this->mentoringSlotId)
                ->willReturn($this->mentoringSlot);
    }
    
    protected function execute()
    {
        $this->bookedMentoringSlotRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->bookedMentoringSlotId);
        $this->task->execute($this->participant);
    }
    public function test_execute_addBookedMentoringSlotByParticipantToRepository()
    {
        $this->participant->expects($this->once())
                ->method('bookMentoringSlot')
                ->with($this->bookedMentoringSlotId, $this->mentoringSlot)
                ->willReturn($this->bookedMentoringSlot);
        
        $this->bookedMentoringSlotRepository->expects($this->once())
                ->method('add')
                ->with($this->bookedMentoringSlot);
        $this->execute();
    }
    public function test_execute_setBookedMentoringSlotId()
    {
        $this->execute();
        $this->assertSame($this->bookedMentoringSlotId, $this->task->bookedMentoringSlotId);
    }
}
