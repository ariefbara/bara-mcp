<?php

namespace Participant\Application\Service\Client;

use Tests\src\Participant\Application\Service\Client\OKRPeriodTestBase;

class CancelOKRPeriodTest extends OKRPeriodTestBase
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CancelOKRPeriod($this->clientParticipantRepository, $this->okrPeriodRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->participantId, $this->okrPeriodId);
    }
    public function test_execute_clientParticipantCancelOKRPeriod()
    {
        $this->clientParticipant->expects($this->once())
                ->method('cancelOKRPeriod')
                ->with($this->okrPeriod);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientParticipantRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
