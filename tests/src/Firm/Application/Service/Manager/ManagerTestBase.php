<?php

namespace Tests\src\Firm\Application\Service\Manager;

use Firm\Application\Service\Manager\ManagerRepository;
use Firm\Domain\Model\Firm\Manager;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class ManagerTestBase extends TestBase
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
    protected $firmId = "firmId", $managerId = "managerId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfClass(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method("aManagerInFirm")
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);
    }

}
