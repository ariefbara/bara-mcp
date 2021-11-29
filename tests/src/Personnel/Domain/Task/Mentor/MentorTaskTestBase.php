<?php

namespace Tests\src\Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequest;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequest\NegotiatedMentoring;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringRequest\NegotiatedMentoringRepository;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringRequestRepository;
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
    
    /**
     * 
     * @var MockObject
     */
    protected $mentoringRequestRepository;
    /**
     * 
     * @var MockObject
     */
    protected $mentoringRequest;
    protected $mentoringRequestId = 'mentoringRequestId';
    protected function setupMentoringRequestRelatedTask()
    {
        $this->mentoringRequest = $this->buildMockOfClass(MentoringRequest::class);
        $this->mentoringRequestRepository = $this->buildMockOfInterface(MentoringRequestRepository::class);
        $this->mentoringRequestRepository->expects($this->any())
                ->method('ofId')
                ->with($this->mentoringRequestId)
                ->willReturn($this->mentoringRequest);
    }
    
    /**
     * 
     * @var MockObject
     */
    protected $negotiatedMentoringRepository;
    /**
     * 
     * @var MockObject
     */
    protected $negotiatedMentoring;
    protected $negotiatedMentoringId = 'negotiatedMentoringId';
    protected function setupNegotiatedMentoringRelatedTask()
    {
        $this->negotiatedMentoring = $this->buildMockOfClass(NegotiatedMentoring::class);
        $this->negotiatedMentoringRepository = $this->buildMockOfInterface(NegotiatedMentoringRepository::class);
        $this->negotiatedMentoringRepository->expects($this->any())
                ->method('ofId')
                ->with($this->negotiatedMentoringId)
                ->willReturn($this->negotiatedMentoring);
    }
}
