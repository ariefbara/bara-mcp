<?php

namespace Tests\src\Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Application\Service\Firm\Personnel\ProgramConsultantRepository;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class MentorTestBase extends TestBase
{

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
    protected $firmId = 'firmId', $personnelId = 'personnelId', $mentorId = 'mentorId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->mentor = $this->buildMockOfClass(ProgramConsultant::class);
        $this->mentorRepository = $this->buildMockOfInterface(ProgramConsultantRepository::class);
        $this->mentorRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->personnelId, $this->mentorId)
                ->willReturn($this->mentor);
    }

}
