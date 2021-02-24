<?php

namespace Tests\src\Bara\Application\Service;

use Bara\Application\Service\AdminRepository;
use Bara\Domain\Model\Admin;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class AdminTestBase extends TestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $adminRepository;
    /**
     * 
     * @var MockObject
     */
    protected $admin;
    protected $adminId = 'adminId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->adminRepository = $this->buildMockOfInterface(AdminRepository::class);
        $this->admin = $this->buildMockOfClass(Admin::class);
        $this->adminRepository->expects($this->any())
                ->method('ofId')
                ->with($this->adminId)
                ->willReturn($this->admin);
    }
}
