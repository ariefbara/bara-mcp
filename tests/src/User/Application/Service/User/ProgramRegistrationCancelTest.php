<?php

namespace User\Application\Service\User;

use Tests\TestBase;
use User\Domain\Model\User\ProgramRegistration;

class ProgramRegistrationCancelTest extends TestBase
{
    protected $programRegistrationRepository, $programRegistration, 
            $clientId = 'clientId', $programRegistrationId = 'programRegistration-id';

    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programRegistration = $this->buildMockOfClass(ProgramRegistration::class);
        $this->programRegistrationRepository = $this->buildMockOfInterface(ProgramRegistrationRepository::class);
        $this->programRegistrationRepository->expects($this->any())
            ->method('ofId')
            ->with($this->clientId, $this->programRegistrationId)
            ->willReturn($this->programRegistration);
        
        $this->service = new CancelProgramRegistration($this->programRegistrationRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->clientId, $this->programRegistrationId);
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
