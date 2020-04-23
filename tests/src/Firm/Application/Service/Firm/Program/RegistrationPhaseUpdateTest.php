<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\ {
    RegistrationPhase,
    RegistrationPhaseData
};
use Tests\TestBase;

class RegistrationPhaseUpdateTest extends TestBase
{
    protected $service;
    protected $programCompositionId;
    protected $registrationPhaseRepository, $registrationPhase, $registrationPhaseId = 'registrationPhaseId';
    protected $registrationPhaseData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programCompositionId = $this->buildMockOfClass(ProgramCompositionId::class);
        $this->registrationPhase = $this->buildMockOfClass(RegistrationPhase::class);
        $this->registrationPhaseRepository = $this->buildMockOfInterface(RegistrationPhaseRepository::class);
        $this->registrationPhaseRepository->expects($this->any())
                ->method('ofId')
                ->with($this->programCompositionId, $this->registrationPhaseId)
                ->willReturn($this->registrationPhase);
        $this->service = new RegistrationPhaseUpdate($this->registrationPhaseRepository);
        
        $this->registrationPhaseData = $this->buildMockOfClass(RegistrationPhaseData::class);
        $this->registrationPhaseData->expects($this->any())
                ->method('getName')
                ->willReturn('new registration phase name');
    }
    
    protected function execute()
    {
        $this->service->execute($this->programCompositionId, $this->registrationPhaseId, $this->registrationPhaseData);
    }
    
    public function test_execute_updateRegistrationPhase()
    {
        $this->registrationPhase->expects($this->once())
                ->method('update')
                ->with($this->registrationPhaseData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->registrationPhaseRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
