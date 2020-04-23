<?php

namespace Bara\Application\Service;

use Bara\Domain\Model\ {
    Admin,
    AdminData
};
use Tests\TestBase;

class AdminChangeProfileTest extends TestBase
{
    protected $service;
    protected $adminRepository, $admin, $adminId = 'adminId';
    protected $adminData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->adminRepository = $this->buildMockOfInterface(AdminRepository::class);
        $this->admin = $this->buildMockOfClass(Admin::class);
        $this->adminRepository->expects($this->any())
                ->method('ofId')
                ->with($this->adminId)
                ->willReturn($this->admin);
        
        $this->service = new AdminChangeProfile($this->adminRepository);
        
        $this->adminData = $this->buildMockOfClass(AdminData::class);
        $this->adminData->expects($this->any())
                ->method('getName')
                ->willReturn('name');
        $this->adminData->expects($this->any())
                ->method('getEmail')
                ->willReturn('admin@email.org');
    }
    
    protected function execute()
    {
        $this->adminRepository->expects($this->any())
                ->method('isEmailAvailable')
                ->willReturn(true);
        $this->service->execute($this->adminId, $this->adminData);
    }
    public function test_execute_updateAdminInput()
    {
        $this->admin->expects($this->once())
                ->method('updateProfile')
                ->with($this->adminData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->adminRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
    public function test_execute_emailUnavailable_throwEx()
    {
        $this->adminRepository->expects($this->once())
                ->method('isEmailAvailable')
                ->with($this->adminData->getEmail())
                ->willReturn(false);
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "conflict: email already registered";
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }
    public function test_execute_emailUnchainged_processNormally()
    {
        $this->adminRepository->expects($this->any())
                ->method('isEmailAvailable')
                ->with($this->adminData->getEmail())
                ->willReturn(false);
        $this->admin->expects($this->once())
                ->method('emailEquals')
                ->with($this->adminData->getEmail())
                ->willReturn(true);
        $this->execute();
    }
}
