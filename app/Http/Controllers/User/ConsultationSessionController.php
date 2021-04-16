<?php

namespace App\Http\Controllers\User;

use Query\Application\Service\User\ViewConsultationSession;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;
use Query\Infrastructure\QueryFilter\ConsultationSessionFilter;

class ConsultationSessionController extends UserBaseController
{
    public function showAll()
    {
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession::class);
        $service = new ViewConsultationSession($this->userQueryRepository(), $consultationSessionRepository);
        
        $consultationSessionFilter = (new ConsultationSessionFilter())
                ->setMinStartTime($this->dateTimeImmutableOfQueryRequest("minStartTime"))
                ->setMaxEndTime($this->dateTimeImmutableOfQueryRequest("maxEndTime"))
                ->setContainParticipantFeedback($this->filterBooleanOfQueryRequest("containParticipantFeedback"))
                ->setContainConsultantFeedback($this->filterBooleanOfQueryRequest("containConsultantFeedback"));
        
        $consultationSessions = $service->showAll(
                $this->userId(), $this->getPage(), $this->getPageSize(), $consultationSessionFilter);
        
        $result = [];
        $result['total'] = count($consultationSessions);
        foreach ($consultationSessions as $consultationSession) {
            $result['list'][] = $this->arrayDataOfConsultationSession($consultationSession);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfConsultationSession(ConsultationSession $consultationSession): array
    {
        return [
            "id" => $consultationSession->getId(),
            "startTime" => $consultationSession->getStartTime(),
            "endTime" => $consultationSession->getEndTime(),
            "media" => $consultationSession->getMedia(),
            "address" => $consultationSession->getAddress(),
            "hasParticipantFeedback" => !empty($consultationSession->getParticipantFeedback()),
            "participant" => [
                "id" => $consultationSession->getParticipant()->getId(),
                "program" => [
                    "id" => $consultationSession->getParticipant()->getProgram()->getId(),
                    "name" => $consultationSession->getParticipant()->getProgram()->getName(),
                ],
            ],
            "consultant" => [
                "id" => $consultationSession->getConsultant()->getId(),
                'personnel' => [
                    "id" => $consultationSession->getConsultant()->getPersonnel()->getId(),
                    "name" => $consultationSession->getConsultant()->getPersonnel()->getName(),
                ],
            ],
        ];
    }
}
