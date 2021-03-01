<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Tests\src\Participant\Application\Service\Client\AsTeamMember\OKRPeriodBaseTest;

class CreateOKRPeriodTest extends OKRPeriodBaseTest
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CreateOKRPeriod($this->teamMemberRepository, $this->teamParticipantRepository, $this->okrPeriodRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->clientId, $this->teamId, $this->teamParticipantId, $this->okrPeriodData);
    }
    public function test_execute_addOKRPeriodCreatedByTeamMemberToRepository()
    {
        $this->teamMember->expects($this->once())
                ->method('createOKRPeriodInTeamParticipant')
                ->with($this->teamParticipant, $this->nextOKRPeriodId, $this->okrPeriodData)
                ->willReturn($this->okrPeriod);
        $this->okrPeriodRepository->expects($this->once())
                ->method('add')
                ->with($this->okrPeriod);
        $this->execute();
    }
    public function test_execute_returnNextOKRPeriodId()
    {
        $this->assertEquals($this->nextOKRPeriodId, $this->execute());
    }
}
