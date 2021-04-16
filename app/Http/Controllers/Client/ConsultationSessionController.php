<?php

namespace App\Http\Controllers\Client;

use Query\Application\Service\Client\ViewConsultationSession;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Infrastructure\QueryFilter\ConsultationSessionFilter;

class ConsultationSessionController extends ClientBaseController
{
    public function showAll()
    {
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession::class);
        $service = new ViewConsultationSession($this->clientQueryRepository(), $consultationSessionRepository);
        
        $consultationSessionFilter = (new ConsultationSessionFilter())
                ->setMinStartTime($this->dateTimeImmutableOfQueryRequest("minStartTime"))
                ->setMaxEndTime($this->dateTimeImmutableOfQueryRequest("maxEndTime"))
                ->setContainParticipantFeedback($this->filterBooleanOfQueryRequest("containParticipantFeedback"))
                ->setContainConsultantFeedback($this->filterBooleanOfQueryRequest("containConsultantFeedback"));
        
        $consultationSessions = $service->showAll(
                $this->firmId(), $this->clientId(), $this->getPage(), $this->getPageSize(), $consultationSessionFilter);
        
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
                "team" => $this->arrayDataOfTeam($consultationSession->getParticipant()->getTeamParticipant()),
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
    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant)? null: [
            "id" => $teamParticipant->getTeam()->getId(),
            "name" => $teamParticipant->getTeam()->getName(),
        ];
    }
}
