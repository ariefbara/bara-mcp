<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Personnel;
use Tests\TestBase;

class CreatePersonnelResetPasswordMailTest extends TestBase
{
    protected $personnelMailRepository, $nextId = "nextId";
    protected $personnelRepository, $personnel;
    protected $service;
    protected $personnelId = "personnelId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->personnelMailRepository = $this->buildMockOfInterface(PersonnelMailRepository::class);
        $this->personnelMailRepository->expects($this->any())
                ->method("nextIdentity")
                ->willReturn($this->nextId);
        
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->personnelRepository = $this->buildMockOfInterface(PersonnelRepository::class);
        $this->personnelRepository->expects($this->any())
                ->method("ofId")
                ->with($this->personnelId)
                ->willReturn($this->personnel);
        
        $this->service = new CreatePersonnelResetPasswordMail($this->personnelMailRepository, $this->personnelRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->personnelId);
    }
    public function test_execute_addPersonnelMailToRepository()
    {
        $this->personnel->expects($this->once())
                ->method("createResetPasswordMail")
                ->with($this->nextId);
        $this->personnelMailRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
}
