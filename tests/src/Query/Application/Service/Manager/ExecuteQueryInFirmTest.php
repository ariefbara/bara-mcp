<?php

namespace Query\Application\Service\Manager;

use Query\Domain\Model\Firm\ManagerQueryInFirm;
use Tests\src\Query\Application\Service\Manager\ManagerServiceTestBase;

class ExecuteQueryInFirmTest extends ManagerServiceTestBase
{
    protected $service;
    //
    protected $query, $payload = 'query payload';

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExecuteQueryInFirm($this->managerRepository);
        //
        $this->query = $this->buildMockOfInterface(ManagerQueryInFirm::class);
    }
    
    //
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->query, $this->payload);
    }
    public function test_execute_managerExecuteQueryInFirm()
    {
        $this->manager->expects($this->once())
                ->method('executeQueryInFirm')
                ->with($this->query, $this->payload);
        $this->execute();
    }
    
}
