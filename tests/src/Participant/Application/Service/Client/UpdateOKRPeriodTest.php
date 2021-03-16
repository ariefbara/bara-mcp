<?php

namespace Participant\Application\Service\Client;

use Tests\src\Participant\Application\Service\Client\OKRPeriodTestBase;

class UpdateOKRPeriodTest extends OKRPeriodTestBase
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UpdateOKRPeriod($this->clientParticipantRepository, $this->okrPeriodRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->participantId, $this->okrPeriodId, $this->okrPeriodData);
    }
    public function test_execute_clientParticipantUpdateOkrPeriod()
    {
        $this->clientParticipant->expects($this->once())
                ->method('updateOKRPeriod')
                ->with($this->okrPeriod, $this->okrPeriodData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientParticipantRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
