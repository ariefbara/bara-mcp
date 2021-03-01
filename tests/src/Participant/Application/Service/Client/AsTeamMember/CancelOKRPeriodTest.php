<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Tests\src\Participant\Application\Service\Client\AsTeamMember\OKRPeriodBaseTest;

class CancelOKRPeriodTest extends OKRPeriodBaseTest
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CancelOKRPeriod(
                $this->teamMemberRepository, $this->teamParticipantRepository, $this->okrPeriodRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute(
                $this->firmId, $this->clientId, $this->teamId, $this->teamParticipantId, $this->okrPeriodId);
    }
    public function test_execute_teamMemberDisableOKRPeriod()
    {
        $this->teamMember->expects($this->once())
                ->method('cancelOKRPeriod')
                ->with($this->teamParticipant, $this->okrPeriod);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->teamMemberRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
