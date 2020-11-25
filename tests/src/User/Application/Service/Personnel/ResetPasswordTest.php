<?php

namespace User\Application\Service\Personnel;

use Tests\TestBase;
use User\Domain\Model\Personnel;

class ResetPasswordTest extends TestBase
{
    protected $personnelRepository, $personnel;
    protected $service;
    protected $firmIdentifier = "firmIdentifier", $email = "personnel@email.org", $resetPasswordCode = "resetPasswordCode", 
            $password = "newPassword123";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->personnelRepository = $this->buildMockOfInterface(PersonnelRepository::class);
        $this->personnelRepository->expects($this->any())
                ->method("aPersonnelInFirmByEmailAndIdentifier")
                ->with($this->firmIdentifier, $this->email)
                ->willReturn($this->personnel);
        
        $this->service = new ResetPassword($this->personnelRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmIdentifier, $this->email, $this->resetPasswordCode, $this->password);
    }
    public function test_execute_resetPersonnelPassword()
    {
        $this->personnel->expects($this->once())
                ->method("resetPassword")
                ->with($this->resetPasswordCode, $this->password);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->personnelRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
