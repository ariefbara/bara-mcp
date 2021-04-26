<?php

namespace App\Http\Controllers\User;

use Query\Application\Service\User\ViewConsultationRequest;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest;
use Query\Infrastructure\QueryFilter\ConsultationRequestFilter;

class ConsultationRequestController extends UserBaseController
{
    public function showAll()
    {
        $consultationRequestRepository = $this->em->getRepository(ConsultationRequest::class);
        $service = new ViewConsultationRequest($this->userQueryRepository(), $consultationRequestRepository);
        
        $status = $this->request->query("status") == null ?
                null : filter_var_array($this->request->query("status"), FILTER_SANITIZE_STRING);
        $consultationRequestFilter = (new ConsultationRequestFilter())
                ->setMinStartTime($this->dateTimeImmutableOfQueryRequest("minStartTime"))
                ->setMaxEndTime($this->dateTimeImmutableOfQueryRequest("maxEndTime"))
                ->setConcludedStatus($this->filterBooleanOfInputRequest("concludedStatus"))
                ->setStatus($status);
        
        $consultationRequests = $service->showAll(
                $this->userId(), $this->getPage(), $this->getPageSize(), $consultationRequestFilter);
        
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
}
