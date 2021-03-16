<?php

namespace Participant\Application\Service\User;

use Tests\src\Participant\Application\Service\User\OKRPeriodTestBase;

class CancelOKRPeriodTest extends OKRPeriodTestBase
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CancelOKRPeriod($this->userParticipantRepository, $this->okrPeriodRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->userId, $this->participantId, $this->okrPeriodId);
    }
    public function test_execute_userParticipantCancelOKRPeriod()
    {
        $this->userParticipant->expects($this->once())
                ->method('cancelOKRPeriod')
                ->with($this->okrPeriod);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->userParticipantRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
