<?php

namespace Participant\Application\Service\User;

use Tests\src\Participant\Application\Service\User\OKRPeriodTestBase;

class UpdateOKRPeriodTest extends OKRPeriodTestBase
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UpdateOKRPeriod($this->userParticipantRepository, $this->okrPeriodRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->userId, $this->participantId, $this->okrPeriodId, $this->okrPeriodData);
    }
    public function test_execute_userParticipantUpdateOkrPeriod()
    {
        $this->userParticipant->expects($this->once())
                ->method('updateOKRPeriod')
                ->with($this->okrPeriod, $this->okrPeriodData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->userParticipantRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
