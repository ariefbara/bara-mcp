<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlot;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlotData;
use Personnel\Domain\Model\Firm\Program\ConsultationSetup;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringSlotRepository;
use Personnel\Domain\Task\Dependency\Firm\Program\ConsultationSetupRepository;
use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class CreateMultipleMentoringSlotTaskTest extends MentorTaskTestBase
{

    protected $mentoringSlotRepository;
    protected $consultationSetupRepository, $consultationSetup, $consultationSetupId = 'consultationSetupId';
    protected $payload, $mentoringSlotDataOne, $mentoringSlotDataTwo;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mentoringSlotRepository = $this->buildMockOfInterface(MentoringSlotRepository::class);

        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultationSetupRepository = $this->buildMockOfInterface(ConsultationSetupRepository::class);
        $this->consultationSetupRepository->expects($this->any())
                ->method('ofId')
                ->with($this->consultationSetupId)
                ->willReturn($this->consultationSetup);

        $this->payload = new CreateMultipleMentoringSlotPayload($this->consultationSetupId);
        $this->mentoringSlotDataOne = $this->buildMockOfClass(MentoringSlotData::class);
        $this->mentoringSlotDataTwo= $this->buildMockOfClass(MentoringSlotData::class);
        $this->payload->addMentoringSlotData($this->mentoringSlotDataOne);
        $this->payload->addMentoringSlotData($this->mentoringSlotDataTwo);

        $this->task = new CreateMultipleMentoringSlotTask(
                $this->mentoringSlotRepository, $this->consultationSetupRepository, $this->payload);
    }

    protected function execute()
    {
        $this->task->execute($this->mentor);
    }
    public function test_execute_addSlotsCreatedByMentorToRepository()
    {
        $mentoringSlotOne = $this->buildMockOfClass(MentoringSlot::class);
        $mentoringSlotTwo = $this->buildMockOfClass(MentoringSlot::class);
        
        $this->mentoringSlotRepository->expects($this->exactly(2))
                ->method('nextIdentity')
                ->willReturnOnConsecutiveCalls($idOne = 'idOne', $idTwo = 'idTwo');
        
        $this->mentor->expects($this->exactly(2))
                ->method('createMentoringSlot')
                ->withConsecutive(
                        [$idOne, $this->consultationSetup, $this->mentoringSlotDataOne], 
                        [$idTwo, $this->consultationSetup, $this->mentoringSlotDataTwo])
                ->willReturnOnConsecutiveCalls($mentoringSlotOne, $mentoringSlotTwo);
        
        $this->mentoringSlotRepository->expects($this->exactly(2))
                ->method('add')
                ->withConsecutive([$mentoringSlotOne], [$mentoringSlotTwo]);
        $this->execute();
    }

}
