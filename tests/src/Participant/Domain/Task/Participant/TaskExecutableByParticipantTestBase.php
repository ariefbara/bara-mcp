<?php

namespace Tests\src\Participant\Domain\Task\Participant;

use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\DeclaredMentoring;
use Participant\Domain\Model\Participant\MentoringRequest;
use Participant\Domain\Model\Participant\MentoringRequest\NegotiatedMentoring;
use Participant\Domain\Task\Dependency\Firm\Program\ConsultationSetupRepository;
use Participant\Domain\Task\Dependency\Firm\Program\MentorRepository;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\DeclaredMentoringRepository;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequest\NegotiatedMentoringRepository;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequestRepository;
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
    
    /**
     * 
     * @var MockObject
     */
    protected $mentorRepository;
    /**
     * 
     * @var MockObject
     */
    protected $mentor;
    protected $mentorId = 'mentorId';
    protected function setMentorRelatedAsset(): void
    {
        $this->mentor = $this->buildMockOfClass(Consultant::class);
        $this->mentorRepository = $this->buildMockOfInterface(MentorRepository::class);
        $this->mentorRepository->expects($this->any())
                ->method('ofId')
                ->with($this->mentorId)
                ->willReturn($this->mentor);
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
    protected function setMentoringRequestRelatedAsset(): void
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
    protected $consultationSetupRepository;
    /**
     * 
     * @var MockObject
     */
    protected $consultationSetup;
    protected $consultationSetupId = 'consultationSetupId';
    protected function setConsultationSetupRelatedAsset(): void
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
    protected $negotiatedMentoringRepository;
    /**
     * 
     * @var MockObject
     */
    protected $negotiatedMentoring;
    protected $negotiatedMentoringId = 'negotiatedMentoringId';
    protected function setNegotiatedMentoringRelatedAsset(): void
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
    protected function setDeclaredMentoringRelatedAsset(): void
    {
        $this->declaredMentoring = $this->buildMockOfClass(DeclaredMentoring::class);
        $this->declaredMentoringRepository = $this->buildMockOfInterface(DeclaredMentoringRepository::class);
        $this->declaredMentoringRepository->expects($this->any())
                ->method('ofId')
                ->with($this->declaredMentoringId)
                ->willReturn($this->declaredMentoring);
    }
}
