<?php

namespace Tests\src\Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DeclaredMentoring;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequest;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequest\NegotiatedMentoring;
use Personnel\Domain\Model\Firm\Program\ConsultationSetup;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\DeclaredMentoringRepository;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringRequest\NegotiatedMentoringRepository;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringRequestRepository;
use Personnel\Domain\Task\Dependency\Firm\Program\ConsultationSetupRepository;
use Personnel\Domain\Task\Dependency\Firm\Program\ParticipantRepository;
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
    protected function setMentoringRequestRelatedTask()
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
    protected function setNegotiatedMentoringRelatedTask()
    {
        $this->negotiatedMentoring = $this->buildMockOfClass(NegotiatedMentoring::class);
        $this->negotiatedMentoringRepository = $this->buildMockOfInterface(NegotiatedMentoringRepository::class);
        $this->negotiatedMentoringRepository->expects($this->any())
                ->method('ofId')
                ->with($this->negotiatedMentoringId)
                ->willReturn($this->negotiatedMentoring);
    }
    
    /**
     * 
     * @var MockObject
     */
    protected $declaredMentoringRepository;
    /**
     * 
     * @var MockObject
     */
    protected $declaredMentoring;
    protected $declaredMentoringId = 'declaredMentoringId';
    protected function setDeclaredMentoringRelatedTask()
    {
        $this->declaredMentoring = $this->buildMockOfClass(DeclaredMentoring::class);
        $this->declaredMentoringRepository = $this->buildMockOfInterface(DeclaredMentoringRepository::class);
        $this->declaredMentoringRepository->expects($this->any())
                ->method('ofId')
                ->with($this->declaredMentoringId)
                ->willReturn($this->declaredMentoring);
    }
    
    /**
     * 
     * @var MockObject
     */
    protected $consultationSetupRepository;
    /**
     * 
     * @var MockObject
     */
    protected $consultationSetup;
    protected $consultationSetupId = 'consultationSetupId';
    protected function setConsultationSetupRelatedTask()
    {
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultationSetupRepository = $this->buildMockOfInterface(ConsultationSetupRepository::class);
        $this->consultationSetupRepository->expects($this->any())
                ->method('ofId')
                ->with($this->consultationSetupId)
                ->willReturn($this->consultationSetup);
    }
    
    /**
     * 
     * @var MockObject
     */
    protected $participantRepository;
    /**
     * 
     * @var MockObject
     */
    protected $participant;
    protected $participantId = 'participantId';
    protected function setParticipantRelatedTask()
    {
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        $this->participantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->participantId)
                ->willReturn($this->participant);
    }
}
