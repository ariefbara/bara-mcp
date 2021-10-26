<?php

namespace Tests\src\Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\ConsultationSessionRepository;
use PHPUnit\Framework\MockObject\MockObject;

class ConsultationSessionTaskTestBase extends MentorTaskTestBase
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

        $this->consultationSessionRepository = $this->buildMockOfClass(ConsultationSessionRepository::class);

        $this->consultationSessionRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->consultationSessionId);

        $this->consultationSessionRepository->expects($this->any())
                ->method('aConsultationSessionOfId')
                ->with($this->consultationSessionId)
                ->willReturn($this->consultationSession);
    }

}
