<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\ConsultationSetup;
use Tests\TestBase;

class ConsultationSetupRemoveTest extends TestBase
{

    protected $consultationSetupRepository, $consultationSetup, $programCompositionId, $consultationSetupId = 'consultationSetup-id';
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->programCompositionId = $this->buildMockOfClass(ProgramCompositionId::class);
        $this->consultationSetupRepository = $this->buildMockOfInterface(ConsultationSetupRepository::class);
        $this->consultationSetupRepository->expects($this->any())
            ->method('ofId')
            ->with($this->programCompositionId, $this->consultationSetupId)
            ->willReturn($this->consultationSetup);

        $this->service = new ConsultationSetupRemove($this->consultationSetupRepository);
    }

    protected function execute()
    {
        $this->service->execute($this->programCompositionId, $this->consultationSetupId);
    }

    public function test_execute_removeConsultationSetup()
    {
        $this->consultationSetup->expects($this->once())
            ->method('remove');
        $this->execute();
    }

    public function test_execute_updateRepository()
    {
        $this->consultationSetupRepository->expects($this->once())
            ->method('update');
        $this->execute();
    }

}
