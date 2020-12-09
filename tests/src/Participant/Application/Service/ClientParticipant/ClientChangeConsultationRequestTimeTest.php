<?php

namespace Participant\Application\Service\ClientParticipant;

use Participant\Application\Service\ClientParticipantRepository;
use Participant\Domain\Model\ClientParticipant;
use Participant\Domain\Model\Participant\ConsultationRequestData;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ClientChangeConsultationRequestTimeTest extends TestBase
{

    protected $service;
    protected $clientParticipantRepository, $clientParticipant;
    protected $dispatcher;
    protected $firmId = 'firmId', $clientId = 'clientId', $programId = 'programId', $consultationRequestId = 'negotiate-schedule-id';
    protected $consultationRequestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->clientParticipantRepository = $this->buildMockOfInterface(ClientParticipantRepository::class);
        $this->clientParticipantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->clientId, $this->programId)
                ->willReturn($this->clientParticipant);
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new ClientChangeConsultationRequestTime($this->clientParticipantRepository, $this->dispatcher);

        $this->consultationRequestData = $this->buildMockOfClass(ConsultationRequestData::Class);
    }

    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->programId, $this->consultationRequestId,
                $this->consultationRequestData);
    }

    public function test_execute_reProposeNegotiateMentoringScheduleInClientParticipant()
    {
        $this->clientParticipant->expects($this->once())
                ->method('reproposeConsultationRequest')
                ->with($this->consultationRequestId, $this->consultationRequestData);
        $this->execute();
    }

    public function test_execute_updateClientParticipantRepository()
    {
        $this->clientParticipantRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }

    public function test_execute_dispatchDispatcher()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->clientParticipant);
        $this->execute();
    }

}
