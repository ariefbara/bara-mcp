<?php

namespace Tests\src\Participant\Domain\Task\Participant;

use Participant\Domain\Model\Participant\ConsultationSession;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\ConsultationSessionRepository;
use PHPUnit\Framework\MockObject\MockObject;

class ConsultationSessionTaskTestBase extends TaskExecutableByParticipantTestBase
{

    /**
     * 
     * @var MockObject
     */
    protected $consultationSessionRepository;

    /**
     * 
     * @var MockObject
     */
    protected $consultationSession;
    protected $consultationSessionId = 'consultationSessionId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultationSessionRepository = $this->buildMockOfInterface(ConsultationSessionRepository::class);

        $this->consultationSessionRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->consultationSessionId);

        $this->consultationSessionRepository->expects($this->any())
                ->method('ofId')
                ->with($this->consultationSessionId)
                ->willReturn($this->consultationSession);
    }

}
