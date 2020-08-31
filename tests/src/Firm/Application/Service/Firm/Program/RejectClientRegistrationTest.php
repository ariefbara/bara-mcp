<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\ClientRegistrant;
use Tests\TestBase;

class RejectClientRegistrationTest extends TestBase
{
    protected $service;
    protected $clientRegistrantRepository, $clientRegistrant;
    
    protected $firmId = 'firmId', $programId = 'programId', $clientRegistrantId = 'clientRegistrantId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRegistrant = $this->buildMockOfClass(ClientRegistrant::class);
        $this->clientRegistrantRepository = $this->buildMockOfInterface(ClientRegistrantRepository::class);
        $this->clientRegistrantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->programId, $this->clientRegistrantId)
                ->willReturn($this->clientRegistrant);
        
        $this->service = new RejectClientRegistration($this->clientRegistrantRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->programId, $this->clientRegistrantId);
    }
    public function test_execute_rejectClientRegistrant()
    {
        $this->clientRegistrant->expects($this->once())
                ->method('reject');
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientRegistrantRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
