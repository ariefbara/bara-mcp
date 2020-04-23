<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\RegistrationPhase;
use Tests\TestBase;

class RegistrationPhaseRemoveTest extends TestBase
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
        $this->service = new RegistrationPhaseRemove($this->registrationPhaseRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->programCompositionId, $this->registrationPhaseId);
    }
    public function test_execute_removeRegistrationPhase()
    {
        $this->registrationPhase->expects($this->once())
                ->method('remove');
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->registrationPhaseRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
