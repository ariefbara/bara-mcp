<?php

namespace App\Http\Controllers\Client;

use Query\Application\Service\Client\ViewConsultationRequest;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Infrastructure\QueryFilter\ConsultationRequestFilter;

class ConsultationRequestController extends ClientBaseController
{
    public function showAll()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest::class);
        $service = new ViewConsultationRequest($this->clientQueryRepository(), $consultationRequestRepository);
        
        $status = $this->request->query("status") == null ?
                null : filter_var_array($this->request->query("status"), FILTER_SANITIZE_STRING);
        $consultationRequestFilter = (new ConsultationRequestFilter())
                ->setMinStartTime($this->dateTimeImmutableOfQueryRequest("minStartTime"))
                ->setMaxEndTime($this->dateTimeImmutableOfQueryRequest("maxEndTime"))
                ->setConcludedStatus($this->filterBooleanOfInputRequest("concludedStatus"))
                ->setStatus($status);
        
        $consultationRequests = $service->showAll(
                $this->firmId(), $this->clientId(), $this->getPage(), $this->getPageSize(), $consultationRequestFilter);
        
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
                "program" => [
                    "id" => $consultationRequest->getParticipant()->getProgram()->getId(),
                    "name" => $consultationRequest->getParticipant()->getProgram()->getName(),
                ],
                "team" => $this->arrayDataOfTeam($consultationRequest->getParticipant()->getTeamParticipant()),
            ],
            "consultant" => [
                "id" => $consultationRequest->getConsultant()->getId(),
                'personnel' => [
                    "id" => $consultationRequest->getConsultant()->getPersonnel()->getId(),
                    "name" => $consultationRequest->getConsultant()->getPersonnel()->getName(),
                ],
            ],
        ];
    }
    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant)? null: [
            "id" => $teamParticipant->getTeam()->getId(),
            "name" => $teamParticipant->getTeam()->getName(),
        ];
    }
}
