<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use Config\EventList;
use DateTimeImmutable;
use Notification\{
    Application\Listener\ConsultationRequestOfferedListener,
    Application\Listener\ConsultationRequestRejectedListener,
    Application\Listener\ConsultationSessionAcceptedByConsultantListener,
    Application\Service\GenerateNotificationWhenConsultationRequestOffered,
    Application\Service\GenerateNotificationWhenConsultationRequestRejected,
    Application\Service\GenerateNotificationWhenConsultationSessionAcceptedByConsultant,
    Domain\Model\Firm\Program\Participant\ConsultationRequest as ConsultationRequest3,
    Domain\Model\Firm\Program\Participant\ConsultationSession
};
use Personnel\{
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestAccept,
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestOffer,
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestReject,
    Domain\Model\Firm\Personnel\ProgramConsultant,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest
};
use Query\{
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestView,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest as ConsultationRequest2,
    Domain\Model\Firm\Team\TeamProgramParticipation,
    Domain\Model\User\UserParticipant,
    Infrastructure\QueryFilter\ConsultationRequestFilter
};
use Resources\Application\Event\Dispatcher;

class ConsultationRequestController extends PersonnelBaseController
{

    public function accept($programConsultationId, $consultationRequestId)
    {
        $service = $this->buildAcceptService();
        $service->execute($this->firmId(), $this->personnelId(), $programConsultationId, $consultationRequestId);

        return $this->show($programConsultationId, $consultationRequestId);
    }

    public function offer($programConsultationId, $consultationRequestId)
    {
        $service = $this->buildOfferService();
        $startTime = new DateTimeImmutable($this->stripTagsInputRequest('startTime'));
        $service->execute($this->firmId(), $this->personnelId(), $programConsultationId, $consultationRequestId,
                $startTime);

        return $this->show($programConsultationId, $consultationRequestId);
    }

    public function reject($programConsultationId, $consultationRequestId)
    {
        $service = $this->buildRejectService();
        $service->execute($this->firmId(), $this->personnelId(), $programConsultationId, $consultationRequestId);

        return $this->commandOkResponse();
    }

    public function show($programConsultationId, $consultationRequestId)
    {
        $service = $this->buildViewService();
        $consultationRequest = $service->showById($this->personnelId(), $programConsultationId, $consultationRequestId);
        return $this->singleQueryResponse($this->arrayDataOfConsultationRequest($consultationRequest));
    }

    public function showAll($programConsultationId)
    {
        $service = $this->buildViewService();

        $status = $this->request->query("status") == null ?
                null : filter_var_array($this->request->query("status"), FILTER_SANITIZE_STRING);
        $consultationRequestFilter = (new ConsultationRequestFilter())
                ->setMinStartTime($this->dateTimeImmutableOfQueryRequest("minStartTime"))
                ->setMaxEndTime($this->dateTimeImmutableOfQueryRequest("maxEndTime"))
                ->setConcludedStatus($this->filterBooleanOfInputRequest("concludedStatus"))
                ->setStatus($status);

        $consultationRequests = $service->showAll(
                $this->personnelId(), $programConsultationId, $this->getPage(), $this->getPageSize(),
                $consultationRequestFilter);

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
            "startTime" => $consultationRequest->getStartTimeString(),
            "endTime" => $consultationRequest->getEndTimeString(),
            "concluded" => $consultationRequest->isConcluded(),
            "status" => $consultationRequest->getStatus(),
            "consultationSetup" => [
                "id" => $consultationRequest->getConsultationSetup()->getId(),
                "name" => $consultationRequest->getConsultationSetup()->getName(),
            ],
            "participant" => [
                "id" => $consultationRequest->getParticipant()->getId(),
                "client" => $this->arrayDataOfClient($consultationRequest->getParticipant()->getClientParticipant()),
                "user" => $this->arrayDataOfUser($consultationRequest->getParticipant()->getUserParticipant()),
                "team" => $this->arrayDataOfTeam($consultationRequest->getParticipant()->getTeamParticipant()),
            ],
        ];
    }

    protected function arrayDataOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant) ? null : [
            "id" => $clientParticipant->getClient()->getId(),
            "name" => $clientParticipant->getClient()->getFullName(),
        ];
    }

    protected function arrayDataOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant) ? null : [
            "id" => $userParticipant->getUser()->getId(),
            "name" => $userParticipant->getUser()->getFullName(),
        ];
    }

    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant) ? null : [
            "id" => $teamParticipant->getTeam()->getId(),
            "name" => $teamParticipant->getTeam()->getName(),
        ];
    }

    protected function buildAcceptService()
    {
        $programConsultantRepository = $this->em->getRepository(ProgramConsultant::class);

        $dispatcher = new Dispatcher();
        $dispatcher->addListener(
                EventList::CONSULTATION_SESSION_SCHEDULED_BY_CONSULTANT,
                $this->buildConsultationSessionAcceptedByConsultantListener());

        return new ConsultationRequestAccept($programConsultantRepository, $dispatcher);
    }

    protected function buildConsultationSessionAcceptedByConsultantListener()
    {
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession::class);
        $service = new GenerateNotificationWhenConsultationSessionAcceptedByConsultant($consultationSessionRepository);
        $sendImmediateMail = $this->buildSendImmediateMail();
        return new ConsultationSessionAcceptedByConsultantListener($service, $sendImmediateMail);
    }

    protected function buildOfferService()
    {
        $programConsultantRepository = $this->em->getRepository(ProgramConsultant::class);

        $dispatcher = new Dispatcher();
        $dispatcher->addListener(
                EventList::CONSULTATION_REQUEST_OFFERED, $this->buildConsultationRequestOfferedListener());

        return new ConsultationRequestOffer($programConsultantRepository, $dispatcher);
    }

    protected function buildConsultationRequestOfferedListener()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest3::class);
        $service = new GenerateNotificationWhenConsultationRequestOffered($consultationRequestRepository);
        $sendImmediateMail = $this->buildSendImmediateMail();
        return new ConsultationRequestOfferedListener($service, $sendImmediateMail);
    }

    protected function buildRejectService()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest::class);

        $dispatcher = new Dispatcher();
        $dispatcher->addListener(
                EventList::CONSULTATION_REQUEST_REJECTED, $this->buildConsultationRequestRejectedListener());

        return new ConsultationRequestReject($consultationRequestRepository, $dispatcher);
    }

    protected function buildConsultationRequestRejectedListener()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest3::class);
        $service = new GenerateNotificationWhenConsultationRequestRejected($consultationRequestRepository);
        $sendImmediateMail = $this->buildSendImmediateMail();
        return new ConsultationRequestRejectedListener($service, $sendImmediateMail);
    }

    protected function buildViewService()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest2::class);
        return new ConsultationRequestView($consultationRequestRepository);
    }

}
