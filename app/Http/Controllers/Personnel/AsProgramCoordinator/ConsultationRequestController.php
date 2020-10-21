<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use Query\ {
    Application\Service\Firm\Program\ViewConsultationRequest,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest,
    Domain\Model\Firm\Team\TeamProgramParticipation,
    Domain\Model\User\UserParticipant,
    Infrastructure\QueryFilter\ConsultationRequestFilter
};

class ConsultationRequestController extends AsProgramCoordinatorBaseController
{
    public function showAll($programId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildViewService();
        $status = $this->request->query("status") == null ?
                null : filter_var_array($this->request->query("status"), FILTER_SANITIZE_STRING);
        $consultationRequestFilter = (new ConsultationRequestFilter())
                ->setMinStartTime($this->dateTimeImmutableOfQueryRequest("minStartTime"))
                ->setMaxEndTime($this->dateTimeImmutableOfQueryRequest("maxEndTime"))
                ->setConcludedStatus($this->filterBooleanOfQueryRequest("concludedStatus"))
                ->setStatus($status);
        
        $consultationRequests = $service->showAll(
                $programId, $this->getPage(), $this->getPageSize(), $consultationRequestFilter);
        
        $result = [];
        $result["total"] = count($consultationRequests);
        foreach ($consultationRequests as $consultationRequest) {
            $result["list"][] = $this->arrayDataOfConsultationRequest($consultationRequest);
        }
        return $this->listQueryResponse($result);
    }
    public function show($programId, $consultationRequestId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildViewService();
        $consultationRequest = $service->showById($programId, $consultationRequestId);
        return $this->singleQueryResponse($this->arrayDataOfConsultationRequest($consultationRequest));
    }
    
    protected function arrayDataOfConsultationRequest(ConsultationRequest $consultationRequest): array
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
                "duration" => $consultationRequest->getConsultationSetup()->getSessionDuration(),
            ],
            "consultant" => [
                "id" => $consultationRequest->getConsultant()->getId(),
                "personnel" => [
                    "id" => $consultationRequest->getConsultant()->getPersonnel()->getId(),
                    "name" => $consultationRequest->getConsultant()->getPersonnel()->getName(),
                ],
            ],
            "participant" => [
                "id" => $consultationRequest->getParticipant()->getId(),
                "enrolledTime" => $consultationRequest->getParticipant()->getEnrolledTimeString(),
                "active" => $consultationRequest->getParticipant()->isActive(),
                "note" => $consultationRequest->getParticipant()->getNote(),
                "client" => $this->arrayDataOfClient($consultationRequest->getParticipant()->getClientParticipant()),
                "team" => $this->arrayDataOfTeam($consultationRequest->getParticipant()->getTeamParticipant()),
                "user" => $this->arrayDataOfUser($consultationRequest->getParticipant()->getUserParticipant()),
            ],
        ];
    }
    protected function arrayDataOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant)? null: [
            "id" => $clientParticipant->getClient()->getId(),
            "name" => $clientParticipant->getClient()->getFullName(),
        ];
    }
    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant)? null: [
            "id" => $teamParticipant->getTeam()->getId(),
            "name" => $teamParticipant->getTeam()->getName(),
        ];
    }
    protected function arrayDataOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant)? null: [
            "id" => $userParticipant->getUser()->getId(),
            "name" => $userParticipant->getUser()->getFullName(),
        ];
    }
    
    protected function buildViewService()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest::class);
        return new ViewConsultationRequest($consultationRequestRepository);
    }
}
