<?php

namespace Participant\Domain\Task\Participant;

use Tests\src\Participant\Domain\Task\Participant\TaskExecutableByParticipantTestBase;

class RequestMentoringTaskTest extends TaskExecutableByParticipantTestBase
{
    protected $mentoringRequestData;
    protected $task;
    protected function setUp(): void
    {
        parent::setUp();
        $this->setupMentoringRequestRelatedAsset();
        $this->setupMentorRelatedAsset();
        $this->setupConsultationSetupRelatedAsset();
        
        $this->mentoringRequestData = new \Participant\Domain\Model\Participant\MentoringRequestData(
                new \DateTimeImmutable('+24 hours'), 
                'online', 
                'meet.googel.com/nksdf2132n');
        $payload = new RequestMentoringPayload($this->mentorId, $this->consultationSetupId, $this->mentoringRequestData);
        $this->task = new RequestMentoringTask(
                $this->mentoringRequestRepository, $this->mentorRepository, $this->consultationSetupRepository, 
                $payload);
    }
    
    protected function execute()
    {
        $this->mentoringRequestRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->mentoringRequestId);
        $this->task->execute($this->participant);
    }
    public function test_execute_addMentoringRequestFromParticipantToRepository()
    {
        $this->participant->expects($this->once())
                ->method('requestMentoring')
                ->with($this->mentoringRequestId, $this->mentor, $this->consultationSetup, $this->mentoringRequestData)
                ->willReturn($this->mentoringRequest);
        $this->mentoringRequestRepository->expects($this->once())
                ->method('add')
                ->with($this->mentoringRequest);
        $this->execute();
    }
    public function test_execute_setRequestMentoringRequestId()
    {
        $this->execute();
        $this->assertSame($this->mentoringRequestId, $this->task->requestedMentoringId);
    }
}
