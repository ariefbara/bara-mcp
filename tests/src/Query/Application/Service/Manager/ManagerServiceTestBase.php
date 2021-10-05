<?php

namespace Tests\src\Query\Application\Service\Manager;

use PHPUnit\Framework\MockObject\MockObject;
use Query\Application\Service\Manager\ManagerRepository;
use Query\Domain\Model\Firm\Manager;
use Tests\TestBase;

class ManagerServiceTestBase extends TestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $managerRepository;
    /**
     * 
     * @var MockObject
     */
    protected $manager;
    protected $firmId = 'firm-id', $managerId = 'manager-id';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method('aManagerInFirm')
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);
    }
}
