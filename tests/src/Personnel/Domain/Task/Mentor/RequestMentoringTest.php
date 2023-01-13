<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequestData;
use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class RequestMentoringTest extends MentorTaskTestBase {
    protected $task;
    protected $payload, $mentoringRequestData;
    
    protected function setUp(): void {
        parent::setUp();
        $this->setMentoringRequestRelatedTask();
        $this->setConsultationSetupRelatedTask();
        $this->setParticipantRelatedTask();
        
        $this->task = new RequestMentoring(
                $this->mentoringRequestRepository, $this->consultationSetupRepository, 
                $this->participantRepository);
        
        $this->mentoringRequestData = $this->buildMockOfClass(MentoringRequestData::class);
        $this->payload = new RequestMentoringPayload($this->mentoringRequestData, $this->consultationSetupId, $this->participantId);
    }
    
    protected function execute() {
        $this->mentoringRequestRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->mentoringRequestId);
        $this->task->execute($this->mentor, $this->payload);
    }
    public function test_execute_addMentoringRequestRequestedByMentorToRepository() {
        $this->mentor->expects($this->once())
                ->method('requestMentoring')
                ->with($this->mentoringRequestId, $this->consultationSetup, $this->participant, $this->mentoringRequestData)
                ->willReturn($this->mentoringRequest);
        
        $this->mentoringRequestRepository->expects($this->once())
                ->method('add')
                ->with($this->mentoringRequest);
        $this->execute();
    }
    public function test_execute_setRequestedMentoringIdInPayload() {
        $this->execute();
        $this->assertSame($this->mentoringRequestId, $this->payload->requestedMentoringId);
    }
}
