<?php

namespace App\Http\Controllers\Personnel\ProgramConsultant;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use Client\{
    Application\Listener\ConsultationRequestNotificationLIstener,
    Application\Listener\ConsultationSessionNotificationListener,
    Application\Service\Client\ProgramParticipation\ConsultationRequest\ConsultationRequestNotificationAdd,
    Application\Service\Client\ProgramParticipation\ConsultationSession\ConsultationSessionNotificationAdd,
    Domain\Model\Client\ProgramParticipation\ConsultationRequest as ConsultationRequestInClient,
    Domain\Model\Client\ProgramParticipation\ConsultationRequest\ConsultationRequestNotification,
    Domain\Model\Client\ProgramParticipation\ConsultationSession,
    Domain\Model\Client\ProgramParticipation\ConsultationSession\ConsultationSessionNotification
};
use DateTimeImmutable;
use Personnel\{
    Application\Service\Firm\Personnel\PersonnelCompositionId,
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestAccept,
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestOffer,
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestReject,
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestView,
    Application\Service\Firm\Personnel\ProgramConsultant\ProgramConsultantCompositionId,
    Domain\Event\ConsultationRequestMutatedByConsultantEvent,
    Domain\Event\ConsultationSessionMutatedByConsultantEvent,
    Domain\Model\Firm\Personnel\ProgramConsultant,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest
};
use Resources\Application\Event\Dispatcher;

class ConsultationRequestController extends PersonnelBaseController
{

    public function accept($programConsultantId, $consultationRequestId)
    {
        $service = $this->buildAcceptService();
        $personnelCompositionId = new PersonnelCompositionId($this->firmId(), $this->personnelId());
        $service->execute($personnelCompositionId, $programConsultantId, $consultationRequestId);

        return $this->show($programConsultantId, $consultationRequestId);
    }

    public function offer($programConsultantId, $consultationRequestId)
    {
        $service = $this->buildOfferService();
        $personnelCompositionId = new PersonnelCompositionId($this->firmId(), $this->personnelId());
        $startTime = new DateTimeImmutable($this->stripTagsInputRequest('startTime'));
        $service->execute($personnelCompositionId, $programConsultantId, $consultationRequestId, $startTime);

        return $this->show($programConsultantId, $consultationRequestId);
    }

    public function reject($programConsultantId, $consultationRequestId)
    {
        $service = $this->buildRejectService();
        $programConsultantCompositionId = new ProgramConsultantCompositionId(
                $this->firmId(), $this->personnelId(), $programConsultantId);
        $service->execute($programConsultantCompositionId, $consultationRequestId);

        return $this->commandOkResponse();
    }

    public function show($programConsultantId, $consultationRequestId)
    {
        $service = $this->buildViewService();
        $programConsultantCompositionId = new ProgramConsultantCompositionId(
                $this->firmId(), $this->personnelId(), $programConsultantId);
        $consultationRequest = $service->showById($programConsultantCompositionId, $consultationRequestId);

        return $this->singleQueryResponse($this->arrayDataOfConsultationRequest($consultationRequest));
    }

    public function showAll($programConsultantId)
    {
        $service = $this->buildViewService();
        $programConsultantCompositionId = new ProgramConsultantCompositionId(
                $this->firmId(), $this->personnelId(), $programConsultantId);
        $consultationRequests = $service->showAll($programConsultantCompositionId, $this->getPage(),
                $this->getPageSize());

        $result = [];
        $result['total'] = count($consultationRequests);
        foreach ($consultationRequests as $consultationRequest) {
            $result['list'][] = $this->arrayDataOfConsultationRequest($consultationRequest);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfConsultationRequest(ConsultationRequest $consultationRequest): array
    {
        return [
            "id" => $consultationRequest->getId(),
            "startTime" => $consultationRequest->getStartTimeString(),
            "endTime" => $consultationRequest->getEndTimeString(),
            "concluded" => $consultationRequest->isConcluded(),
            "status" => $consultationRequest->getStatusString(),
            "consultationSetup" => [
                "id" => $consultationRequest->getConsultationSetup()->getId(),
                "name" => $consultationRequest->getConsultationSetup()->getName(),
            ],
            "participant" => [
                "id" => $consultationRequest->getParticipant()->getId(),
                "client" => [
                    "id" => $consultationRequest->getParticipant()->getClient()->getId(),
                    "name" => $consultationRequest->getParticipant()->getClient()->getName(),
                ],
            ],
        ];
    }

    protected function buildAcceptService()
    {
        $programConsultantRepository = $this->em->getRepository(ProgramConsultant::class);
        $dispatcher = new Dispatcher();

        $consultationSesionNotificationRepository = $this->em->getRepository(ConsultationSessionNotification::class);
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession::class);
        $consultationSessionNotificationAdd = new ConsultationSessionNotificationAdd(
                $consultationSesionNotificationRepository, $consultationSessionRepository);
        $listener = new ConsultationSessionNotificationListener($consultationSessionNotificationAdd);
        $dispatcher->addListener(
                ConsultationSessionMutatedByConsultantEvent::EVENT_NAME, $listener);

        return new ConsultationRequestAccept($programConsultantRepository, $dispatcher);
    }

    protected function buildOfferService()
    {
        $programConsultantRepository = $this->em->getRepository(ProgramConsultant::class);
        $dispatcher = new Dispatcher();

        $consultationRequestNotificationRepository = $this->em->getRepository(ConsultationRequestNotification::class);
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequestInClient::class);
        $consultationRequestNotificationAdd = new ConsultationRequestNotificationAdd(
                $consultationRequestNotificationRepository, $consultationRequestRepository);
        $listener = new ConsultationRequestNotificationLIstener($consultationRequestNotificationAdd);
        $dispatcher->addListener(
                ConsultationRequestMutatedByConsultantEvent::EVENT_NAME, $listener);

        return new ConsultationRequestOffer($programConsultantRepository, $dispatcher);
    }

    protected function buildRejectService()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest::class);
        return new ConsultationRequestReject($consultationRequestRepository);
    }

    protected function buildViewService()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest::class);
        return new ConsultationRequestView($consultationRequestRepository);
    }

}
