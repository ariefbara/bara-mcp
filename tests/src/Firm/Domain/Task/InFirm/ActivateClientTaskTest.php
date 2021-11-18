<?php

namespace Firm\Domain\Task\InFirm;

use Tests\src\Firm\Domain\Task\InFirm\ClientRelatedTaskTestBase;

class ActivateClientTaskTest extends ClientRelatedTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->task = new ActivateClientTask($this->clientRepository, $this->clientId);
    }
    
    protected function executeInFirm()
    {
        $this->task->executeInFirm($this->firm);
    }
    public function test_executeInFirm_activateClient()
    {
        $this->client->expects($this->once())
                ->method('activate');
        $this->executeInFirm();
    }
    public function test_executeInFirm_assertClientManageableInFirm()
    {
        $this->client->expects($this->once())
                ->method('assertManageableInFirm')
                ->with($this->firm);
        $this->executeInFirm();
    }
}
