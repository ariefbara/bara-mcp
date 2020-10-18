<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\Client\ClientBaseController;
use Config\EventList;
use DateTimeImmutable;
use Notification\ {
    Application\Listener\ConsultationRequestCancelledListener,
    Application\Listener\ConsultationRequestSubmittedListener,
    Application\Listener\ConsultationRequestTimeChangedListener,
    Application\Listener\ConsultationSessionScheduledByParticipantListener,
    Application\Service\GenerateNotificationWhenConsultationRequestCancelled,
    Application\Service\GenerateNotificationWhenConsultationRequestSubmitted,
    Application\Service\GenerateNotificationWhenConsultationRequestTimeChanged,
    Application\Service\GenerateNotificationWhenConsultationSessionScheduledByParticipant,
    Domain\Model\Firm\Program\Participant\ConsultationRequest as ConsultationRequest3,
    Domain\Model\Firm\Program\Participant\ConsultationSession
};
use Participant\ {
    Application\Service\ClientParticipant\ClientAcceptConsultationRequest,
    Application\Service\ClientParticipant\ClientCancelConcultationRequest,
    Application\Service\ClientParticipant\ClientChangeConsultationRequestTime,
    Application\Service\ClientParticipant\ClientSubmitConsultationRequest,
    Domain\DependencyModel\Firm\Program\Consultant,
    Domain\DependencyModel\Firm\Program\ConsultationSetup,
    Domain\Model\ClientParticipant,
    Domain\Model\Participant\ConsultationRequest as ConsultationRequest2
};
use Query\ {
    Application\Service\Firm\Client\ProgramParticipation\ViewConsultationRequest,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest,
    Infrastructure\QueryFilter\ConsultationRequestFilter
};
use Resources\Application\Event\Dispatcher;

class ConsultationRequestController extends ClientBaseController
{

    public function submit($programParticipationId)
    {
        $service = $this->buildProposeService();

        $consultationSetupId = $this->stripTagsInputRequest('consultationSetupId');
        $consultantId = $this->stripTagsInputRequest('consultantId');
        $startTime = new DateTimeImmutable($this->stripTagsInputRequest('startTime'));

        $consultationRequestId = $service->execute(
                $this->firmId(), $this->clientId(), $programParticipationId, $consultationSetupId, $consultantId,
                $startTime);

        $viewService = $this->buildViewService();
        $consultationRequest = $viewService
                ->showById($this->firmId(), $this->clientId(), $programParticipationId, $consultationRequestId);
        return $this->commandCreatedResponse($this->arrayDataOfConsultationRequest($consultationRequest));
    }

    public function cancel($programParticipationId, $consultationRequestId)
    {
        $service = $this->buildCancelService();
        $service->execute($this->firmId(), $this->clientId(), $programParticipationId, $consultationRequestId);
        return $this->commandOkResponse();
    }

    public function changeTime($programParticipationId, $consultationRequestId)
    {
        $service = $this->buildChangeTimeService();
        $startTime = new DateTimeImmutable($this->stripTagsInputRequest('startTime'));
        $service->execute($this->firmId(), $this->clientId(), $programParticipationId, $consultationRequestId,
                $startTime);

        return $this->show($programParticipationId, $consultationRequestId);
    }

    public function accept($programParticipationId, $consultationRequestId)
    {
        $service = $this->buildAcceptService();
        $service->execute($this->firmId(), $this->clientId(), $programParticipationId, $consultationRequestId);

        return $this->show($programParticipationId, $consultationRequestId);
    }

    public function show($programParticipationId, $consultationRequestId)
    {
        $service = $this->buildViewService();
        $consultationRequest = $service->showById($this->firmId(), $this->clientId(), $programParticipationId,
                $consultationRequestId);
        return $this->singleQueryResponse($this->arrayDataOfConsultationRequest($consultationRequest));
    }

    public function showAll($programParticipationId)
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
                $this->firmId(), $this->clientId(), $programParticipationId, $this->getPage(), $this->getPageSize(),
                $consultationRequestFilter);

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

    protected function buildViewService()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest::class);
        return new ViewConsultationRequest($consultationRequestRepository);
    }

    protected function buildProposeService()
    {

        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest2::class);
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        $consultationSetupRepository = $this->em->getRepository(ConsultationSetup::class);
        $consultantRepository = $this->em->getRepository(Consultant::class);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(
                EventList::CONSULTATION_REQUEST_SUBMITTED, $this->buildConsultationRequestSubmittedListener());

        return new ClientSubmitConsultationRequest(
                $consultationRequestRepository, $clientParticipantRepository, $consultationSetupRepository,
                $consultantRepository, $dispatcher);
    }

    protected function buildConsultationRequestSubmittedListener()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest3::class);
        $service = new GenerateNotificationWhenConsultationRequestSubmitted($consultationRequestRepository);
        return new ConsultationRequestSubmittedListener($service, $this->buildSendImmediateMail());
    }

    protected function buildCancelService()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest2::class);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(
                EventList::CONSULTATION_REQUEST_CANCELLED, $this->buildConsultationRequestCancelledListener());

        return new ClientCancelConcultationRequest($consultationRequestRepository, $dispatcher);
    }

    protected function buildConsultationRequestCancelledListener()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest3::class);
        $service = new GenerateNotificationWhenConsultationRequestCancelled($consultationRequestRepository);
        return new ConsultationRequestCancelledListener($service, $this->buildSendImmediateMail());
    }

    protected function buildAcceptService()
    {

        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(
                EventList::OFFERED_CONSULTATION_REQUEST_ACCEPTED,
                $this->buildConsultationSessionScheduledByParticipantListener());

        return new ClientAcceptConsultationRequest($clientParticipantRepository, $dispatcher);
    }

    protected function buildConsultationSessionScheduledByParticipantListener()
    {
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession::class);
        $service = new GenerateNotificationWhenConsultationSessionScheduledByParticipant($consultationSessionRepository);
        return new ConsultationSessionScheduledByParticipantListener($service, $this->buildSendImmediateMail());
    }

    protected function buildChangeTimeService()
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(
                EventList::CONSULTATION_REQUEST_TIME_CHANGED, $this->buildConsultationRequestTimeChangedListener());

        return new ClientChangeConsultationRequestTime($clientParticipantRepository, $dispatcher);
    }

    protected function buildConsultationRequestTimeChangedListener()
    {
        $consulationRequestRepository = $this->em->getRepository(ConsultationRequest3::class);
        $service = new GenerateNotificationWhenConsultationRequestTimeChanged($consulationRequestRepository);
        return new ConsultationRequestTimeChangedListener($service, $this->buildSendImmediateMail());
    }

}
