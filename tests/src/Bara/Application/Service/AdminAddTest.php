<?php

namespace Bara\Application\Service;

use Bara\Domain\Model\AdminData;
use Tests\TestBase;

class AdminAddTest extends TestBase
{
    protected $service;
    protected $adminRepository;
    protected $adminData;
    protected $password;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->adminRepository = $this->buildMockOfInterface(AdminRepository::class);
        $this->service = new AdminAdd($this->adminRepository);
        
        $this->password = 'password123';
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
        $this->adminRepository->expects($this->once())
                ->method('isEmailAvailable')
                ->willReturn(true);
        return $this->service->execute($this->adminData, $this->password);
    }
    
    public function test_execute_addAdminToRepository()
    {
        $this->adminRepository->expects($this->once())
                ->method('add');
        $this->execute();
    }
    public function test_execute_emailUnavailable()
    {
        $this->adminRepository->expects($this->once())
                ->method('isEmailAvailable')
                ->with($this->adminData->getEmail())
                ->willReturn(false);
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "conflict: email already registered";
        $this->assertRegularExceptionThrowed($operation, "Conflict", $errorDetail);
    }
}
