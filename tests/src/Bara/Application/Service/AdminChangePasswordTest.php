<?php

namespace Bara\Application\Service;

use Bara\Domain\Model\Admin;
use Tests\TestBase;

class AdminChangePasswordTest extends TestBase
{
    protected $service;
    protected $adminRepository, $admin, $adminId = 'adminId';
    protected $previousPassword = 'password123', $newPassword = 'newPassword123';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->adminRepository = $this->buildMockOfInterface(AdminRepository::class);
        $this->admin = $this->buildMockOfClass(Admin::class);
        $this->adminRepository->expects($this->any())
                ->method('ofId')
                ->with($this->adminId)
                ->willReturn($this->admin);
        
        $this->service = new AdminChangePassword($this->adminRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->adminId, $this->previousPassword, $this->newPassword);
    }
    
    public function test_execute_changeAdminPassword()
    {
        $this->admin->expects($this->once())
                ->method('changePassword')
                ->with($this->previousPassword, $this->newPassword);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->adminRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
