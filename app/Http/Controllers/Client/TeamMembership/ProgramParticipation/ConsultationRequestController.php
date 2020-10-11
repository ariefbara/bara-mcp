<?php

namespace App\Http\Controllers\Client\TeamMembership\ProgramParticipation;

use App\Http\Controllers\Client\TeamMembership\TeamMembershipBaseController;
use Config\EventList;
use DateTimeImmutable;
use Notification\{
    Application\Listener\Firm\Team\MemberAcceptedOfferedConsultationRequestListener,
    Application\Listener\Firm\Team\MemberCancelledConsultationRequestListener,
    Application\Listener\Firm\Team\MemberChangedConsultationRequestTimeListener,
    Application\Listener\Firm\Team\MemberSubmittedConsultationRequestListener,
    Application\Service\AddConsultationSessionScheduledNotificationTriggeredByTeamMember,
    Application\Service\GenerateConsultationRequestNotificationTriggeredByTeamMember,
    Domain\Model\Firm\Program\Participant\ConsultationRequest as ConsultationRequest3,
    Domain\Model\Firm\Program\Participant\ConsultationSession,
    Domain\Model\Firm\Team\Member
};
use Participant\{
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\AcceptOfferedConsultationRequest,
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\CancelConsultationRequest,
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\ChangeConsultationRequestTime,
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\SubmitConsultationRequest,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\DependencyModel\Firm\Program\Consultant,
    Domain\DependencyModel\Firm\Program\ConsultationSetup,
    Domain\Model\Participant\ConsultationRequest as ConsultationRequest2,
    Domain\Model\TeamProgramParticipation
};
use Query\{
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\ViewConsultationRequest,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest,
    Domain\Service\Firm\Program\ConsultationSetup\ConsultationRequestFinder,
    Infrastructure\QueryFilter\ConsultationRequestFilter
};
use Resources\Application\Event\Dispatcher;

class ConsultationRequestController extends TeamMembershipBaseController
{

    public function submit($teamMembershipId, $teamProgramParticipationId)
    {
        $service = $this->buildSubmitService();
        $consultationSetupId = $this->stripTagsInputRequest("consultationSetupId");
        $consultantId = $this->stripTagsInputRequest("consultantId");
        $startTime = new DateTimeImmutable($this->stripTagsInputRequest("startTime"));
        $consultationRequestId = $service->execute(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId,
                $consultationSetupId, $consultantId, $startTime);

        $viewService = $this->buildViewService();
        $consultationRequest = $viewService->showById(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId,
                $consultationRequestId);
        return $this->commandCreatedResponse($this->arrayDataOfConsultationRequest($consultationRequest));
    }

    public function changeTime($teamMembershipId, $teamProgramParticipationId, $consultationRequestId)
    {
        $service = $this->buildChangeTimeService();
        $startTime = new DateTimeImmutable($this->stripTagsInputRequest("startTime"));
        $service->execute(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId,
                $consultationRequestId, $startTime);

        return $this->show($teamMembershipId, $teamProgramParticipationId, $consultationRequestId);
    }

    public function cancel($teamMembershipId, $teamProgramParticipationId, $consultationRequestId)
    {
        $service = $this->buildCancelService();
        $service->execute($this->firmId(), $this->clientId(), $teamMembershipId, $consultationRequestId);
        return $this->commandOkResponse();
    }

    public function accept($teamMembershipId, $teamProgramParticipationId, $consultationRequestId)
    {
        $service = $this->buildAcceptService();
        $service->execute(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId,
                $consultationRequestId);

        return $this->show($teamMembershipId, $teamProgramParticipationId, $consultationRequestId);
    }

    public function show($teamMembershipId, $teamProgramParticipationId, $consultationRequestId)
    {
        $service = $this->buildViewService();
        $consultationRequest = $service->showById(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId,
                $consultationRequestId);
        return $this->singleQueryResponse($this->arrayDataOfConsultationRequest($consultationRequest));
    }

    public function showAll($teamMembershipId, $teamProgramParticipationId)
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
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId, $this->getPage(),
                $this->getPageSize(), $consultationRequestFilter);

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
            "concluded" => $consultationRequest->isConcluded(),
            "status" => $consultationRequest->getStatus(),
        ];
    }

    protected function buildViewService()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest::class);
        $consultationRequestFinder = new ConsultationRequestFinder($consultationRequestRepository);
        return new ViewConsultationRequest($this->teamMembershipRepository(), $consultationRequestFinder);
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
                $this->buildGenerateConsultationRequestNotificationTriggeredByTeamMember(),
                $this->buildSendImmediateMail());
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
                $this->buildGenerateConsultationRequestNotificationTriggeredByTeamMember(),
                $this->buildSendImmediateMail());
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
                $this->buildGenerateConsultationRequestNotificationTriggeredByTeamMember(),
                $this->buildSendImmediateMail());
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
        $listener = new MemberAcceptedOfferedConsultationRequestListener($service, $this->buildSendImmediateMail());

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
