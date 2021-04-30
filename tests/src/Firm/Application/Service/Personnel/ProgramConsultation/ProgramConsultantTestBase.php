<?php

namespace Tests\src\Firm\Application\Service\Personnel\ProgramConsultation;

use Firm\Application\Service\Personnel\ProgramConsultant\ProgramConsultantRepository;
use Firm\Domain\Model\Firm\Program\Consultant;
use Tests\TestBase;

class ProgramConsultantTestBase extends TestBase
{
    protected $consultantRepository;
    protected $consultant;
    protected $firmId = 'firm-id', $personnelId = 'personnel-id', $programId = 'program-id';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultantRepository = $this->buildMockOfInterface(ProgramConsultantRepository::class);
        $this->consultantRepository->expects($this->any())
                ->method('aConsultantCorrespondWithProgram')
                ->with($this->firmId, $this->personnelId, $this->programId)
                ->willReturn($this->consultant);
    }
}
