<?php

namespace App\Http\Controllers\Personnel;

use Query\Application\Service\Personnel\ViewConsultationRequest;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Infrastructure\QueryFilter\ConsultationRequestFilter;

class ConsultationRequestController extends PersonnelBaseController
{

    public function showAll()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest::class);
        $service = new ViewConsultationRequest($this->personnelQueryRepository(), $consultationRequestRepository);

        $status = $this->request->query("status") == null ?
                null : filter_var_array($this->request->query("status"), FILTER_SANITIZE_STRING);
        $consultationRequestFilter = (new ConsultationRequestFilter())
                ->setMinStartTime($this->dateTimeImmutableOfQueryRequest("minStartTime"))
                ->setMaxEndTime($this->dateTimeImmutableOfQueryRequest("maxEndTime"))
                ->setConcludedStatus($this->filterBooleanOfInputRequest("concludedStatus"))
                ->setStatus($status);
        
        $consultationRequests = $service->showAll(
                $this->firmId(), $this->personnelId(), $this->getPage(), $this->getPageSize(),
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
            'consultant' => [
                'id' => $consultationRequest->getConsultant()->getId(),
                'program' => [
                    'id' => $consultationRequest->getConsultant()->getProgram()->getId(),
                    'name' => $consultationRequest->getConsultant()->getProgram()->getName(),
                ],
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

}
