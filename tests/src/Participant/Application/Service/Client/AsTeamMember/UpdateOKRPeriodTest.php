<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Tests\src\Participant\Application\Service\Client\AsTeamMember\OKRPeriodBaseTest;

class UpdateOKRPeriodTest extends OKRPeriodBaseTest
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UpdateOKRPeriod($this->teamMemberRepository, $this->teamParticipantRepository, $this->okrPeriodRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute(
                $this->firmId, $this->clientId, $this->teamId, $this->teamParticipantId, $this->okrPeriodId, $this->okrPeriodData);
    }
    public function test_execute_TeamMemberUpdateOKRPeriod()
    {
        $this->teamMember->expects($this->once())
                ->method('updateOKRPeriod')
                ->with($this->teamParticipant, $this->okrPeriod, $this->okrPeriodData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->teamMemberRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
