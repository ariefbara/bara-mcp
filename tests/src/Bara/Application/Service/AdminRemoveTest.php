<?php

namespace Bara\Application\Service;

use Bara\Domain\Model\Admin;
use Tests\TestBase;

class AdminRemoveTest extends TestBase
{
    protected $service;
    protected $adminRepository, $admin, $adminId = 'adminId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->adminRepository = $this->buildMockOfInterface(AdminRepository::class);
        $this->admin = $this->buildMockOfClass(Admin::class);
        $this->adminRepository->expects($this->any())
                ->method('ofId')
                ->with($this->adminId)
                ->willReturn($this->admin);
        $this->service = new AdminRemove($this->adminRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->adminId);
    }
    public function test_execute_removeAdmin()
    {
        $this->admin->expects($this->once())
                ->method('remove');
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->adminRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
