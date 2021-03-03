<?php

namespace Participant\Application\Service\Client;

use Participant\Application\Service\Client\CreateOKRPeriod;
use Tests\src\Participant\Application\Service\Client\OKRPeriodTestBase;

class CreateOKRPeriodTest extends OKRPeriodTestBase
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CreateOKRPeriod($this->clientParticipantRepository, $this->okrPeriodRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->clientId, $this->participantId, $this->okrPeriodData);
    }
    public function test_execute_addOKRPeriodCreatedByClientParticipantToRepository()
    {
        $this->clientParticipant->expects($this->once())
                ->method('createOKRPeriod')
                ->with($this->nextId, $this->okrPeriodData)
                ->willReturn($this->okrPeriod);
        $this->okrPeriodRepository->expects($this->once())
                ->method('add')
                ->with($this->okrPeriod);
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
}
