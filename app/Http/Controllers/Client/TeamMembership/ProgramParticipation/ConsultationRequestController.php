<?php

namespace App\Http\Controllers\Client\TeamMembership\ProgramParticipation;

use App\Http\Controllers\Client\TeamMembership\TeamMembershipBaseController;
use DateTimeImmutable;
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
    Domain\Service\Firm\Program\ConsultationSetup\ConsultationRequestFinder
};

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
        $service->execute(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId,
                $consultationRequestId);
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
        $status = $this->request->query("status") == null? 
                null: filter_var_array($this->request->query("status"), FILTER_SANITIZE_STRING);
        $consultationRequestFilter = (new \Query\Infrastructure\QueryFilter\ConsultationRequestFilter())
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
        return new SubmitConsultationRequest(
                $consultationRequestRepository, $teamMembershipRepository, $teamProgramParticipationRepository,
                $consultationSetupRepository, $consultantRepository);
    }

    protected function buildChangeTimeService()
    {
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        $teamProgramParticipationRepository = $this->em->getRepository(TeamProgramParticipation::class);
        return new ChangeConsultationRequestTime($teamMembershipRepository, $teamProgramParticipationRepository);
    }

    protected function buildCancelService()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest2::class);
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        $teamProgramParticipationRepository = $this->em->getRepository(TeamProgramParticipation::class);
        return new CancelConsultationRequest(
                $consultationRequestRepository, $teamMembershipRepository, $teamProgramParticipationRepository);
    }

    protected function buildAcceptService()
    {
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        $teamProgramParticipationRepository = $this->em->getRepository(TeamProgramParticipation::class);
        return new AcceptOfferedConsultationRequest($teamMembershipRepository, $teamProgramParticipationRepository);
    }

}
