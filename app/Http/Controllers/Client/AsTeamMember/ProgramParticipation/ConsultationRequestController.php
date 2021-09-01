<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use Config\EventList;
use DateTimeImmutable;
use Notification\Application\Listener\Firm\Team\MemberAcceptedOfferedConsultationRequestListener;
use Notification\Application\Listener\Firm\Team\MemberCancelledConsultationRequestListener;
use Notification\Application\Listener\Firm\Team\MemberChangedConsultationRequestTimeListener;
use Notification\Application\Listener\Firm\Team\MemberSubmittedConsultationRequestListener;
use Notification\Application\Service\AddConsultationSessionScheduledNotificationTriggeredByTeamMember;
use Notification\Application\Service\GenerateConsultationRequestNotificationTriggeredByTeamMember;
use Notification\Domain\Model\Firm\Program\Participant\ConsultationRequest as ConsultationRequest3;
use Notification\Domain\Model\Firm\Program\Participant\ConsultationSession;
use Notification\Domain\Model\Firm\Team\Member;
use Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\AcceptOfferedConsultationRequest;
use Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\CancelConsultationRequest;
use Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\ChangeConsultationRequestTime;
use Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\SubmitConsultationRequest;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\Model\Participant\ConsultationRequest as ConsultationRequest2;
use Participant\Domain\Model\Participant\ConsultationRequestData;
use Participant\Domain\Model\TeamProgramParticipation;
use Query\Application\Service\Firm\Team\ProgramParticipation\ViewConsultationRequest;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest;
use Query\Infrastructure\QueryFilter\ConsultationRequestFilter;
use Resources\Application\Event\Dispatcher;

class ConsultationRequestController extends AsTeamMemberBaseController
{

    public function submit($teamId, $teamProgramParticipationId)
    {
        $service = $this->buildSubmitService();
        $consultationSetupId = $this->stripTagsInputRequest("consultationSetupId");
        $consultantId = $this->stripTagsInputRequest("consultantId");

        $consultationRequestId = $service->execute(
                $this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $consultationSetupId,
                $consultantId, $this->getConsultationRequestData());

        $viewService = $this->buildViewService();
        $consultationRequest = $viewService->showById($teamId, $consultationRequestId);
        
        $response = $this->commandCreatedResponse($this->arrayDataOfConsultationRequest($consultationRequest));
        $this->sendAndCloseConnection($response, $this->buildSendImmediateMailJob());
    }

    public function changeTime($teamId, $teamProgramParticipationId, $consultationRequestId)
    {
        $service = $this->buildChangeTimeService();
        
        $service->execute(
                $this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $consultationRequestId,
                $this->getConsultationRequestData());
        
        $consultationRequest = $this->buildViewService()->showById($teamId, $consultationRequestId);
        
        $response = $this->singleQueryResponse($this->arrayDataOfConsultationRequest($consultationRequest));
        $this->sendAndCloseConnection($response, $this->buildSendImmediateMailJob());
    }

    public function cancel($teamId, $teamProgramParticipationId, $consultationRequestId)
    {
        $service = $this->buildCancelService();
        $service->execute($this->firmId(), $this->clientId(), $teamId, $consultationRequestId);
        
        $response = $this->commandOkResponse();
        $this->sendAndCloseConnection($response, $this->buildSendImmediateMailJob());
    }

    public function accept($teamId, $teamProgramParticipationId, $consultationRequestId)
    {
        $service = $this->buildAcceptService();
        $service->execute(
                $this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $consultationRequestId);
        
        $consultationRequest = $this->buildViewService()->showById($teamId, $consultationRequestId);
        
        $response = $this->singleQueryResponse($this->arrayDataOfConsultationRequest($consultationRequest));
        $this->sendAndCloseConnection($response, $this->buildSendImmediateMailJob());
    }

    protected function getConsultationRequestData()
    {
        $startTime = $this->dateTimeImmutableOfInputRequest("startTime");
        $media = $this->stripTagsInputRequest("media");
        $address = $this->stripTagsInputRequest("address");

        return new ConsultationRequestData($startTime, $media, $address);
    }

    public function show($teamId, $teamProgramParticipationId, $consultationRequestId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);

        $service = $this->buildViewService();
        $consultationRequest = $service->showById($teamId, $consultationRequestId);
        return $this->singleQueryResponse($this->arrayDataOfConsultationRequest($consultationRequest));
    }

    public function showAll($teamId, $teamProgramParticipationId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);

        $service = $this->buildViewService();
        $status = $this->request->query("status") == null ?
                null : filter_var_array($this->request->query("status"), FILTER_SANITIZE_STRING);
        $consultationRequestFilter = (new ConsultationRequestFilter())
                ->setMinStartTime($this->dateTimeImmutableOfQueryRequest("minStartTime"))
                ->setMaxEndTime($this->dateTimeImmutableOfQueryRequest("maxEndTime"))
                ->setConcludedStatus($this->filterBooleanOfInputRequest("concludedStatus"))
                ->setStatus($status);

        $consultationRequests = $service->showAll(
                $teamId, $teamProgramParticipationId, $this->getPage(), $this->getPageSize(), $consultationRequestFilter);

        $result = [];
        $result["total"] = count($consultationRequests);
        foreach ($consultationRequests as $consultationRequest) {
            $result["list"][] = $this->arrayDataOfConsultationRequest($consultationRequest);
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

    protected function buildSubmitService()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest2::class);
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        $teamProgramParticipationRepository = $this->em->getRepository(TeamProgramParticipation::class);
        $consultationSetupRepository = $this->em->getRepository(ConsultationSetup::class);
        $consultantRepository = $this->em->getRepository(Consultant::class);

        $dispatcher = new Dispatcher();
        $listener = new MemberSubmittedConsultationRequestListener(
                $this->buildGenerateConsultationRequestNotificationTriggeredByTeamMember());
        $dispatcher->addListener(EventList::CONSULTATION_REQUEST_SUBMITTED, $listener);

        return new SubmitConsultationRequest(
                $consultationRequestRepository, $teamMembershipRepository, $teamProgramParticipationRepository,
                $consultationSetupRepository, $consultantRepository, $dispatcher);
    }

    protected function buildChangeTimeService()
    {
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        $teamProgramParticipationRepository = $this->em->getRepository(TeamProgramParticipation::class);

        $dispatcher = new Dispatcher();
        $listener = new MemberChangedConsultationRequestTimeListener(
                $this->buildGenerateConsultationRequestNotificationTriggeredByTeamMember());
        $dispatcher->addListener(EventList::CONSULTATION_REQUEST_TIME_CHANGED, $listener);

        return new ChangeConsultationRequestTime($teamMembershipRepository, $teamProgramParticipationRepository,
                $dispatcher);
    }

    protected function buildCancelService()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest2::class);
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);

        $dispatcher = new Dispatcher();
        $listener = new MemberCancelledConsultationRequestListener(
                $this->buildGenerateConsultationRequestNotificationTriggeredByTeamMember());
        $dispatcher->addListener(EventList::CONSULTATION_REQUEST_CANCELLED, $listener);

        return new CancelConsultationRequest($consultationRequestRepository, $teamMembershipRepository, $dispatcher);
    }

    protected function buildAcceptService()
    {
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        $teamProgramParticipationRepository = $this->em->getRepository(TeamProgramParticipation::class);

        $dispatcher = new Dispatcher();
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession::class);
        $memberRepository = $this->em->getRepository(Member::class);
        $service = new AddConsultationSessionScheduledNotificationTriggeredByTeamMember(
                $consultationSessionRepository, $memberRepository);
        $listener = new MemberAcceptedOfferedConsultationRequestListener($service);

        $dispatcher->addListener(EventList::OFFERED_CONSULTATION_REQUEST_ACCEPTED, $listener);
        return new AcceptOfferedConsultationRequest($teamMembershipRepository, $teamProgramParticipationRepository,
                $dispatcher);
    }

    protected function buildGenerateConsultationRequestNotificationTriggeredByTeamMember()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest3::class);
        $memberRepository = $this->em->getRepository(Member::class);
        return new GenerateConsultationRequestNotificationTriggeredByTeamMember(
                $consultationRequestRepository, $memberRepository);
    }

}
