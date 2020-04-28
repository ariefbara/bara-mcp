<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\ {
    Application\Service\Client\ProgramParticipationRepository,
    Application\Service\Firm\Program\ConsultantRepository,
    Application\Service\Firm\Program\ConsultationSetupRepository,
    Domain\Model\Client\ProgramParticipation,
    Domain\Model\Client\ProgramParticipation\ConsultationRequest,
    Domain\Model\Firm\Program\Consultant,
    Domain\Model\Firm\Program\ConsultationSetup
};
use DateTimeImmutable;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ConsultationRequestProposeTest extends TestBase
{

    protected $service;
    protected $consultationRequestRepository;
    protected $clientId = 'clientId';
    protected $programParticipationRepository, $programParticipation, $programParticipationId = 'programParticipation-id';
    protected $consultationSetupRepository, $consultationSetup, $consultationSetupId = 'consultationSetup-id';
    protected $consultantRepository, $consultant, $consultantId = 'consultant-id';
    protected $dispatcher;
    protected $startTime;
    protected $consultationRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequestRepository = $this->buildMockOfInterface(ConsultationRequestRepository::class);

        $this->programParticipation = $this->buildMockOfClass(ProgramParticipation::class);
        $this->programParticipationRepository = $this->buildMockOfClass(ProgramParticipationRepository::class);
        $this->programParticipationRepository->expects($this->any())
                ->method('ofId')
                ->with($this->clientId, $this->programParticipationId)
                ->willReturn($this->programParticipation);

        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultationSetupRepository = $this->buildMockOfInterface(ConsultationSetupRepository::class);
        $this->consultationSetupRepository->expects($this->any())
                ->method('ofId')
                ->with($this->clientId, $this->programParticipationId, $this->consultationSetupId)
                ->willReturn($this->consultationSetup);

        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultantRepository = $this->buildMockOfInterface(ConsultantRepository::class);
        $this->consultantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->clientId, $this->programParticipationId, $this->consultantId)
                ->willReturn($this->consultant);

        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new ConsultationRequestPropose(
                $this->consultationRequestRepository, $this->programParticipationRepository,
                $this->consultationSetupRepository, $this->consultantRepository, $this->dispatcher);

        $this->startTime = new DateTimeImmutable();
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
    }

    protected function execute()
    {
        $this->programParticipation->expects($this->any())
                ->method('createConsultationRequest')
                ->with($this->consultationSetup, $this->consultant, $this->startTime)
                ->willReturn($this->consultationRequest);
        return $this->service->execute(
                        $this->clientId, $this->programParticipationId, $this->consultationSetupId, $this->consultantId,
                        $this->startTime);
    }

    public function test_execute_addConsultationRequestCreatedInProgramParticipationToRepository()
    {
        $this->consultationRequestRepository->expects($this->once())
                ->method('add')
                ->with($this->consultationRequest);
        $this->execute();
    }

    public function test_execute_dispatchConsultationRequestToDispatcher()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->programParticipation);
        $this->execute();
    }

}
