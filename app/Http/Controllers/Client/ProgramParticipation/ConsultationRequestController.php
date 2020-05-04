<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\Client\ClientBaseController;
use Client\{
    Application\Service\Client\ProgramParticipation\ConsultationRequestAccept,
    Application\Service\Client\ProgramParticipation\ConsultationRequestCancel,
    Application\Service\Client\ProgramParticipation\ConsultationRequestPropose,
    Application\Service\Client\ProgramParticipation\ConsultationRequestRepropose,
    Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId,
    Domain\Event\ParticipantMutateConsultationRequestEvent,
    Domain\Event\ParticipantMutateConsultationSessionEvent,
    Domain\Model\Client\ProgramParticipation,
    Domain\Model\Client\ProgramParticipation\ConsultationRequest,
    Domain\Model\Firm\Program\Consultant,
    Domain\Model\Firm\Program\ConsultationSetup
};
use DateTimeImmutable;
use Personnel\{
    Application\Listener\ParticipantMutateConsultationRequestListener,
    Application\Listener\ParticipantMutateConsultationSessionListener,
    Domain\Model\Firm\Personnel\PersonnelNotification,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest as ConsultationRequest3,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession
};
use Query\{
    Application\Service\Client\ProgramParticipation\ConsultationRequestView,
    Domain\Model\Firm\Program\Participant\ConsultationRequest as ConsultationRequest2
};
use Resources\Application\Event\Dispatcher;

class ConsultationRequestController extends ClientBaseController
{

    public function propose($programParticipationId)
    {
        $service = $this->buildProposeService();
        $consultationSetupId = $this->stripTagsInputRequest('consultationSetupId');
        $consultantId = $this->stripTagsInputRequest('consultantId');
        $startTime = new DateTimeImmutable($this->stripTagsInputRequest('startTime'));

        $consultationRequestId = $service->execute(
                $this->clientId(), $programParticipationId, $consultationSetupId, $consultantId, $startTime);

        $viewService = $this->buildViewService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId(
                $this->clientId(), $programParticipationId);
        $consultationRequest = $viewService->showById($programParticipationCompositionId, $consultationRequestId);

        return $this->commandCreatedResponse($this->arrayDataOfConsultationRequest($consultationRequest));
    }

    public function cancel($programParticipationId, $consultationRequestId)
    {
        $service = $this->buildCancelService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId(
                $this->clientId(), $programParticipationId);
        $service->execute($programParticipationCompositionId, $consultationRequestId);
        return $this->commandOkResponse();
    }

    public function rePropose($programParticipationId, $consultationRequestId)
    {
        $service = $this->buildReproposeService();
        $startTime = new DateTimeImmutable($this->stripTagsInputRequest('startTime'));
        $service->execute($this->clientId(), $programParticipationId, $consultationRequestId, $startTime);

        return $this->show($programParticipationId, $consultationRequestId);
    }

    public function accept($programParticipationId, $consultationRequestId)
    {
        $service = $this->buildAcceptService();
        $service->execute($this->clientId(), $programParticipationId, $consultationRequestId);
        return $this->show($programParticipationId, $consultationRequestId);
    }

    public function show($programParticipationId, $consultationRequestId)
    {
        $service = $this->buildViewService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId(
                $this->clientId(), $programParticipationId);
        $consultationRequest = $service->showById($programParticipationCompositionId, $consultationRequestId);

        return $this->singleQueryResponse($this->arrayDataOfConsultationRequest($consultationRequest));
    }

    public function showAll($programParticipationId)
    {
        $service = $this->buildViewService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId(
                $this->clientId(), $programParticipationId);
        $consultationRequests = $service->showAll(
                $programParticipationCompositionId, $this->getPage(), $this->getPageSize());

        $result = [];
        $result['total'] = count($consultationRequests);
        foreach ($consultationRequests as $consultationRequest) {
            $result['list'][] = $this->arrayDataOfConsultationRequest($consultationRequest);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfConsultationRequest(ConsultationRequest2 $consultationRequest): array
    {
        return [
            "id" => $consultationRequest->getId(),
            "consultationSetup" => [
                "id" => $consultationRequest->getConsultationSetup()->getId(),
                "name" => $consultationRequest->getConsultationSetup()->getName(),
            ],
            "consultant" => [
                "id" => $consultationRequest->getConsultant()->getId(),
                "personnel" => [
                    "id" => $consultationRequest->getConsultant()->getPersonnel()->getId(),
                    "name" => $consultationRequest->getConsultant()->getPersonnel()->getName(),
                ],
            ],
            "startTime" => $consultationRequest->getStartTimeString(),
            "endTime" => $consultationRequest->getEndTimeString(),
            "concluded" => $consultationRequest->isConcluded(),
            "status" => $consultationRequest->getStatus(),
        ];
    }

    protected function buildProposeService()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest::class);
        $programParticipationRepository = $this->em->getRepository(ProgramParticipation::class);
        $consultationSetupRepository = $this->em->getRepository(ConsultationSetup::class);
        $consultantRepository = $this->em->getRepository(Consultant::class);
        $dispatcher = new Dispatcher();

        $dispatcher->addListener(
                ParticipantMutateConsultationRequestEvent::EVENT_NAME,
                $this->buildParticipantMutateConsultationRequestListener());

        return new ConsultationRequestPropose(
                $consultationRequestRepository, $programParticipationRepository, $consultationSetupRepository,
                $consultantRepository, $dispatcher);
    }

    protected function buildCancelService()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest::class);
        return new ConsultationRequestCancel($consultationRequestRepository);
    }

    protected function buildAcceptService()
    {
        $programParticipationRepository = $this->em->getRepository(ProgramParticipation::class);
        $dispatcher = new Dispatcher();

        $dispatcher->addListener(
                ParticipantMutateConsultationSessionEvent::EVENT_NAME,
                $this->buildParticipantMutateConsultationSessionListener());

        return new ConsultationRequestAccept($programParticipationRepository, $dispatcher);
    }

    protected function buildReproposeService()
    {
        $programParticipationRepository = $this->em->getRepository(ProgramParticipation::class);
        $dispatcher = new Dispatcher();

        $dispatcher->addListener(
                ParticipantMutateConsultationRequestEvent::EVENT_NAME,
                $this->buildParticipantMutateConsultationRequestListener());

        return new ConsultationRequestRepropose($programParticipationRepository, $dispatcher);
    }

    protected function buildViewService()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest2::class);
        return new ConsultationRequestView($consultationRequestRepository);
    }

    protected function buildParticipantMutateConsultationRequestListener()
    {
        $personnelNotificationRepository = $this->em->getRepository(PersonnelNotification::class);
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest3::class);
        return new ParticipantMutateConsultationRequestListener($personnelNotificationRepository,
                $consultationRequestRepository);
    }

    protected function buildParticipantMutateConsultationSessionListener()
    {
        $personnelNotificationRepository = $this->em->getRepository(PersonnelNotification::class);
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession::class);
        return new ParticipantMutateConsultationSessionListener(
                $personnelNotificationRepository, $consultationSessionRepository);
    }

}
