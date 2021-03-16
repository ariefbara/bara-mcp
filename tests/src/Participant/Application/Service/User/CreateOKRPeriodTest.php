<?php

namespace Participant\Application\Service\User;

use Tests\src\Participant\Application\Service\User\OKRPeriodTestBase;

class CreateOKRPeriodTest extends OKRPeriodTestBase
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CreateOKRPeriod($this->userParticipantRepository, $this->okrPeriodRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->userId, $this->participantId, $this->okrPeriodData);
    }
    public function test_execute_addOKRPeriodCreatedByUserParticipantToRepository()
    {
        $this->userParticipant->expects($this->once())
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
