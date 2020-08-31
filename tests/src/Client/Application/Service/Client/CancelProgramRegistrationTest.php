<?php

namespace Client\Application\Service\Client;

use Client\Domain\Model\Client\ProgramRegistration;
use Tests\TestBase;

class CancelProgramRegistrationTest extends TestBase
{
    protected $service;
    protected $programRegistrationRepository, $programRegistration;
    
    protected $firmId = 'firmId', $clientId = 'clientId', $programRegistrationId = 'programRegistrationId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programRegistration = $this->buildMockOfClass(ProgramRegistration::class);
        $this->programRegistrationRepository = $this->buildMockOfInterface(ProgramRegistrationRepository::class);
        $this->programRegistrationRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->clientId, $this->programRegistrationId)
                ->willReturn($this->programRegistration);
        
        $this->service = new CancelProgramRegistration($this->programRegistrationRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->programRegistrationId);
    }
    public function test_execute_cancelProgramRegistration()
    {
        $this->programRegistration->expects($this->once())
                ->method('cancel');
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->programRegistrationRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
