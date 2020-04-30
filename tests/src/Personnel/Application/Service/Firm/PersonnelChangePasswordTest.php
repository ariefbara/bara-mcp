<?php

namespace Personnel\Application\Service\Firm;

use Personnel\Domain\Model\Firm\Personnel;
use Tests\TestBase;

class PersonnelChangePasswordTest extends TestBase
{
    protected $service;
    protected $firmId = 'firmId';
    protected $personnelRepository, $personnel, $personnelId = 'personnelId';
    protected $previousPassowrd = 'password123', $newPassword = 'newPassword123';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->personnelRepository = $this->buildMockOfInterface(PersonnelRepository::class);
        $this->personnelRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->personnelId)
                ->willReturn($this->personnel);
        
        $this->service = new PersonnelChangePassword($this->personnelRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->personnelId, $this->previousPassowrd, $this->newPassword);
    }
    
    public function test_execute_changePersonnelPassword()
    {
        $this->personnel->expects($this->once())
                ->method('changePassword')
                ->with($this->previousPassowrd, $this->newPassword);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->personnelRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}

