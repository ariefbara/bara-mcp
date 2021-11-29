<?php

namespace Personnel\Domain\Task\Mentor;

use DateTimeImmutable;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequestData;
use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class OfferMentoringRequestTaskTest extends MentorTaskTestBase
{
    protected $mentoringRequestData;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setupMentoringRequestRelatedTask();
        
        $this->mentoringRequestData = new MentoringRequestData(new DateTimeImmutable('+24 hours'), 'media', 'location');
        $this->task = new OfferMentoringRequestTask(
                $this->mentoringRequestRepository, 
                new OfferMentoringRequestPayload($this->mentoringRequestId, $this->mentoringRequestData)
        );
    }
    
    protected function execute()
    {
        $this->task->execute($this->mentor);
    }
    public function test_execute_offerMentoringRequest()
    {
        $this->mentoringRequest->expects($this->once())
                ->method('offer')
                ->with($this->mentoringRequestData);
        $this->execute();
    }
    public function test_execute_assertMentoringRequestBelongsToMentor()
    {
        $this->mentoringRequest->expects($this->once())
                ->method('assertBelongsToMentor')
                ->with($this->mentor);
        $this->execute();
    }
}
