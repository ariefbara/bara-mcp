<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use Config\EventList;
use DateTimeImmutable;
use Notification\Application\Listener\ConsultationRequestOfferedListener;
use Notification\Application\Listener\ConsultationRequestRejectedListener;
use Notification\Application\Listener\ConsultationSessionAcceptedByConsultantListener;
use Notification\Application\Service\GenerateNotificationWhenConsultationRequestOffered;
use Notification\Application\Service\GenerateNotificationWhenConsultationRequestRejected;
use Notification\Application\Service\GenerateNotificationWhenConsultationSessionAcceptedByConsultant;
use Notification\Domain\Model\Firm\Program\Participant\ConsultationRequest as ConsultationRequest3;
use Notification\Domain\Model\Firm\Program\Participant\ConsultationSession;
use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestAccept;
use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestOffer;
use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestReject;
use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ExecuteMentorTask;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequestData;
use Personnel\Domain\Model\Firm\Program\ConsultationSetup;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Task\Mentor\ProposeConsultation;
use Personnel\Domain\Task\Mentor\ProposeConsultationPayload;
use Query\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestView;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest as ConsultationRequest2;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Infrastructure\QueryFilter\ConsultationRequestFilter;
use Resources\Application\Event\Dispatcher;

class ConsultationRequestController extends PersonnelBaseController
{

    public function propose($programConsultationId)
    {
        $dispatcher = new Dispatcher(false);
        $dispatcher->addListener(
                EventList::CONSULTATION_REQUEST_OFFERED, $this->buildConsultationRequestOfferedListener());
        
        $mentorRepository = $this->em->getRepository(ProgramConsultant::class);
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest::class);
        $participantRepository = $this->em->getRepository(Participant::class);
        $consultationSetupRepository = $this->em->getRepository(ConsultationSetup::class);
        $task = new ProposeConsultation($consultationRequestRepository,
                $participantRepository, $consultationSetupRepository, $dispatcher);
        
        $participantId = $this->stripTagsInputRequest('participantId');
        $consultationSetupId = $this->stripTagsInputRequest('consultationSetupId');
        $startTime = $this->dateTimeImmutableOfInputRequest('startTime');
        $media = $this->stripTagsInputRequest('media');
        $address = $this->stripTagsInputRequest('address');
        $consultationRequestData = new ConsultationRequestData($startTime, $media, $address);
        $payload = new ProposeConsultationPayload($participantId, $consultationSetupId, $consultationRequestData);
        
        (new ExecuteMentorTask($mentorRepository))
                ->execute($this->firmId(), $this->personnelId(), $programConsultationId, $task, $payload);
        
        $dispatcher->execute();
        
        $consultationRequest = $this->buildViewService()
                ->showById($this->personnelId(), $programConsultationId, $payload->proposedConsultationRequestId);
        
        $response = $this->singleQueryResponse($this->arrayDataOfConsultationRequest($consultationRequest));        
        
        $this->sendAndCloseConnection($response, $this->buildSendImmediateMailJob());
    }

    public function accept($programConsultationId, $consultationRequestId)
    {
        $service = $this->buildAcceptService();
        $service->execute($this->firmId(), $this->personnelId(), $programConsultationId, $consultationRequestId);

        $consultationRequest = $this->buildViewService()
                ->showById($this->personnelId(), $programConsultationId, $consultationRequestId);

        $response = $this->singleQueryResponse($this->arrayDataOfConsultationRequest($consultationRequest));
        $this->sendAndCloseConnection($response, $this->buildSendImmediateMailJob());
    }

    public function offer($programConsultationId, $consultationRequestId)
    {
        $service = $this->buildOfferService();
        $startTime = new DateTimeImmutable($this->stripTagsInputRequest('startTime'));
        $media = $this->stripTagsInputRequest("media");
        $address = $this->stripTagsInputRequest("address");

        $consultationRequestData = new ConsultationRequestData($startTime, $media, $address);

        $service->execute(
                $this->firmId(), $this->personnelId(), $programConsultationId, $consultationRequestId,
                $consultationRequestData);

        $consultationRequest = $this->buildViewService()
                ->showById($this->personnelId(), $programConsultationId, $consultationRequestId);

        $response = $this->singleQueryResponse($this->arrayDataOfConsultationRequest($consultationRequest));
        $this->sendAndCloseConnection($response, $this->buildSendImmediateMailJob());
    }

    public function reject($programConsultationId, $consultationRequestId)
    {
        $service = $this->buildRejectService();
        $service->execute($this->firmId(), $this->personnelId(), $programConsultationId, $consultationRequestId);

        $response = $this->commandOkResponse();
        $this->sendAndCloseConnection($response, $this->buildSendImmediateMailJob());
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
            "media" => $consultationRequest->getMedia(),
            "address" => $consultationRequest->getAddress(),
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
        return new ConsultationSessionAcceptedByConsultantListener($service);
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
        return new ConsultationRequestOfferedListener($service);
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
        return new ConsultationRequestRejectedListener($service);
    }

    protected function buildViewService()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest2::class);
        return new ConsultationRequestView($consultationRequestRepository);
    }

}
