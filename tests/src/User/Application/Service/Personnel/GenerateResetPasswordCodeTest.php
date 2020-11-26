<?php

namespace User\Application\Service\Personnel;

use Resources\Application\Event\Dispatcher;
use Tests\TestBase;
use User\Domain\Model\Personnel;

class GenerateResetPasswordCodeTest extends TestBase
{
    protected $personnelRepository, $personnel;
    protected $dispatcher;
    protected $service;
    protected $firmIdentifier = "firmIdentifier", $email = "personnel@email.org";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->personnelRepository = $this->buildMockOfInterface(PersonnelRepository::class);
        $this->personnelRepository->expects($this->any())
                ->method("aPersonnelInFirmByEmailAndIdentifier")
                ->with($this->firmIdentifier, $this->email)
                ->willReturn($this->personnel);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new GenerateResetPasswordCode($this->personnelRepository, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmIdentifier, $this->email);
    }
    public function test_execute_generatePersonnelResetPasswordCode()
    {
        $this->personnel->expects($this->once())
                ->method("generateResetPasswordCode");
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->personnelRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
    public function test_excute_dispatchePersonnel()
    {
        $this->dispatcher->expects($this->once())
                ->method("dispatch")
                ->with($this->personnel);
        $this->execute();
    }
}
