<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\Client\ClientBaseController;
use Config\EventList;
use DateTimeImmutable;
use Notification\Application\Listener\ConsultationRequestCancelledListener;
use Notification\Application\Listener\ConsultationRequestSubmittedListener;
use Notification\Application\Listener\ConsultationRequestTimeChangedListener;
use Notification\Application\Listener\ConsultationSessionScheduledByParticipantListener;
use Notification\Application\Service\GenerateNotificationWhenConsultationRequestCancelled;
use Notification\Application\Service\GenerateNotificationWhenConsultationRequestSubmitted;
use Notification\Application\Service\GenerateNotificationWhenConsultationRequestTimeChanged;
use Notification\Application\Service\GenerateNotificationWhenConsultationSessionScheduledByParticipant;
use Notification\Domain\Model\Firm\Program\Participant\ConsultationRequest as ConsultationRequest3;
use Notification\Domain\Model\Firm\Program\Participant\ConsultationSession;
use Participant\Application\Service\ClientParticipant\ClientAcceptConsultationRequest;
use Participant\Application\Service\ClientParticipant\ClientCancelConcultationRequest;
use Participant\Application\Service\ClientParticipant\ClientChangeConsultationRequestTime;
use Participant\Application\Service\ClientParticipant\ClientSubmitConsultationRequest;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\Model\ClientParticipant;
use Participant\Domain\Model\Participant\ConsultationRequest as ConsultationRequest2;
use Participant\Domain\Model\Participant\ConsultationRequestData;
use Query\Application\Service\Firm\Client\ProgramParticipation\ViewConsultationRequest;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest;
use Query\Infrastructure\QueryFilter\ConsultationRequestFilter;
use Resources\Application\Event\Dispatcher;

class ConsultationRequestController extends ClientBaseController
{

    public function submit($programParticipationId)
    {
        $service = $this->buildProposeService();

        $consultationSetupId = $this->stripTagsInputRequest('consultationSetupId');
        $consultantId = $this->stripTagsInputRequest('consultantId');

        $consultationRequestId = $service->execute(
                $this->firmId(), $this->clientId(), $programParticipationId, $consultationSetupId, $consultantId,
                $this->getConsultationRequestData());

        $viewService = $this->buildViewService();
        $consultationRequest = $viewService
                ->showById($this->firmId(), $this->clientId(), $programParticipationId, $consultationRequestId);
        
        $this->sendAndCloseConnection($this->arrayDataOfConsultationRequest($consultationRequest), 201);
        $this->sendImmediateMail();
    }

    public function cancel($programParticipationId, $consultationRequestId)
    {
        $service = $this->buildCancelService();
        $service->execute($this->firmId(), $this->clientId(), $programParticipationId, $consultationRequestId);
        
        $this->sendAndCloseConnection();
        $this->sendImmediateMail();
    }

    public function changeTime($programParticipationId, $consultationRequestId)
    {
        $service = $this->buildChangeTimeService();
        $service->execute(
                $this->firmId(), $this->clientId(), $programParticipationId, $consultationRequestId,
                $this->getConsultationRequestData());
        
        $consultationRequest = $this->buildViewService()
                ->showById($this->firmId(), $this->clientId(), $programParticipationId, $consultationRequestId);
        
        $this->sendAndCloseConnection($this->arrayDataOfConsultationRequest($consultationRequest));
        $this->sendImmediateMail();
    }

    public function accept($programParticipationId, $consultationRequestId)
    {
        $service = $this->buildAcceptService();
        $service->execute($this->firmId(), $this->clientId(), $programParticipationId, $consultationRequestId);
        
        $consultationRequest = $this->buildViewService()
                ->showById($this->firmId(), $this->clientId(), $programParticipationId, $consultationRequestId);
        
        $this->sendAndCloseConnection($this->arrayDataOfConsultationRequest($consultationRequest));
        $this->sendImmediateMail();
    }

    protected function getConsultationRequestData()
    {
        $startTime = $this->dateTimeImmutableOfInputRequest("startTime");
        $media = $this->stripTagsInputRequest("media");
        $address = $this->stripTagsInputRequest("address");

        return new ConsultationRequestData($startTime, $media, $address);
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
            "media" => $consultationRequest->getMedia(),
            "address" => $consultationRequest->getAddress(),
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
        return new ConsultationRequestSubmittedListener($service);
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
        return new ConsultationRequestCancelledListener($service);
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
        return new ConsultationSessionScheduledByParticipantListener($service);
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
        return new ConsultationRequestTimeChangedListener($service);
    }

}
