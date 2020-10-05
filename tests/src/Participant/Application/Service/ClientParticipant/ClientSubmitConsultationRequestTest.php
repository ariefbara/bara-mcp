<?php

namespace Participant\Application\Service\ClientParticipant;

use DateTimeImmutable;
use Participant\ {
    Application\Service\ClientParticipantRepository,
    Application\Service\Participant\ConsultationRequestRepository,
    Domain\DependencyModel\Firm\Program\Consultant,
    Domain\DependencyModel\Firm\Program\ConsultationSetup,
    Domain\Model\ClientParticipant
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ClientSubmitConsultationRequestTest extends TestBase
{

    protected $service;
    protected $consultationRequestRepository, $nextId = 'nextId';
    protected $clientParticipantRepository, $clientParticipant;
    protected $consultationSetupRepository, $consultationSetup;
    protected $consultantRepository, $consultant;
    protected $dispatcher;
    protected $firmId = 'firmId', $clientId = 'clientId', $programParticipationId = 'programParticipationId',
            $consultationSetupId = 'consultationSetupId', $consultantId = 'consultantId', $startTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->consultationRequestRepository = $this->buildMockOfClass(ConsultationRequestRepository::class);
        $this->consultationRequestRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);

        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->clientParticipantRepository = $this->buildMockOfInterface(ClientParticipantRepository::class);
        $this->clientParticipantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->clientId, $this->programParticipationId)
                ->willReturn($this->clientParticipant);

        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultationSetupRepository = $this->buildMockOfInterface(ConsultationSetupRepository::class);
        $this->consultationSetupRepository->expects($this->any())
                ->method('aConsultationSetupInProgramWhereClientParticipate')
                ->with($this->firmId, $this->clientId, $this->programParticipationId, $this->consultationSetupId)
                ->willReturn($this->consultationSetup);

        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultantRepository = $this->buildMockOfInterface(ConsultantRepository::class);
        $this->consultantRepository->expects($this->any())
                ->method("aConsultantInProgramWhereClientParticipate")
                ->with($this->firmId, $this->clientId, $this->programParticipationId, $this->consultantId)
                ->willReturn($this->consultant);

        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new ClientSubmitConsultationRequest(
                $this->consultationRequestRepository, $this->clientParticipantRepository,
                $this->consultationSetupRepository, $this->consultantRepository, $this->dispatcher);

        $this->startTime = new DateTimeImmutable();
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->firmId, $this->clientId, $this->programParticipationId, $this->consultationSetupId,
                        $this->consultantId, $this->startTime);
    }
    public function test_execute_addConsultationRequestFromClientParticipantProposeConsultation()
    {
        $this->clientParticipant->expects($this->once())
                ->method('proposeConsultation')
                ->with($this->nextId, $this->consultationSetup, $this->consultant, $this->startTime);
        
        $this->consultationRequestRepository->expects($this->once())
                ->method('add');
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
    public function test_execute_dispatchClientParticipantToDispatcher()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->clientParticipant);
        $this->execute();
    }

}
