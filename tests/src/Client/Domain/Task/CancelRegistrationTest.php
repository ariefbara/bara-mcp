<?php

namespace Client\Domain\Task;

use Client\Domain\Model\Client\ClientRegistrant;
use Client\Domain\Task\Repository\Firm\Client\ClientRegistrantRepository;
use Tests\src\Client\Domain\Task\ClientTaskTestBase;

class CancelRegistrationTest extends ClientTaskTestBase
{
    protected $clientRegistrantRepository;
    protected $clientRegistrant;
    protected $clientRegistrantId = 'clientRegistrantId';
    
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRegistrantRepository = $this->buildMockOfInterface(ClientRegistrantRepository::class);
        $this->clientRegistrant = $this->buildMockOfClass(ClientRegistrant::class);
        $this->clientRegistrantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->clientRegistrantId)
                ->willReturn($this->clientRegistrant);
        
        $this->task = new CancelRegistration($this->clientRegistrantRepository);
    }
    
    protected function execute()
    {
        $this->task->execute($this->client, $this->clientRegistrantId);
    }
    public function test_execute_cancelRegistration()
    {
        $this->clientRegistrant->expects($this->once())
                ->method('cancel');
        $this->execute();
    }
    public function test_execute_assertRegistrationManageableByClient()
    {
        $this->clientRegistrant->expects($this->once())
                ->method('assertManageableByClient')
                ->with($this->client);
        $this->execute();
    }
}
