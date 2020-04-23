<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\ {
    Application\Service\Firm\ProgramRepository,
    Domain\Model\Firm\Program\RegistrationPhaseData
};
use Tests\TestBase;

class RegistrationPhaseAddTest extends TestBase
{
    protected $service;
    protected $registrationPhaseRepository;
    protected $programRepository;
    protected $registrationPhaseData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->registrationPhaseRepository = $this->buildMockOfInterface(RegistrationPhaseRepository::class);
        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        $this->service = new RegistrationPhaseAdd($this->registrationPhaseRepository, $this->programRepository);
        
        $this->registrationPhaseData = $this->buildMockOfClass(RegistrationPhaseData::class);
        $this->registrationPhaseData->expects($this->any())
                ->method('getName')
                ->willReturn('name');
    }
    
    protected function execute()
    {
        return $this->service->execute('firm-id', 'program-id', $this->registrationPhaseData);
    }
    
    public function test_execute_addRegistrationPhaseToRepository()
    {
        $this->registrationPhaseRepository->expects($this->once())
            ->method('add');
        $this->execute();
    }
}
