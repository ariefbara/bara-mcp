<?php

namespace App\Http\Controllers\Personnel;

use Query\Application\Service\Personnel\ViewConsultationSession;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Infrastructure\QueryFilter\ConsultationSessionFilter;

class ConsultationSessionController extends PersonnelBaseController
{

    public function showAll()
    {
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession::class);
        $service = new ViewConsultationSession($this->personnelQueryRepository(), $consultationSessionRepository);

        $consultationSessionFilter = (new ConsultationSessionFilter())
                ->setMinStartTime($this->dateTimeImmutableOfQueryRequest("minStartTime"))
                ->setMaxEndTime($this->dateTimeImmutableOfQueryRequest("maxEndTime"))
                ->setContainParticipantFeedback($this->filterBooleanOfQueryRequest("containParticipantFeedback"))
                ->setContainConsultantFeedback($this->filterBooleanOfQueryRequest("containConsultantFeedback"));

        $consultationSessions = $service->showAll(
                $this->firmId(), $this->personnelId(), $this->getPage(), $this->getPageSize(),
                $consultationSessionFilter);
        
        $result = [];
        $result['total'] = count($consultationSessions);
        foreach ($consultationSessions as $consultationSession) {
            $result['list'][] = [
                "id" => $consultationSession->getId(),
                "startTime" => $consultationSession->getStartTime(),
                "endTime" => $consultationSession->getEndTime(),
                "media" => $consultationSession->getMedia(),
                "address" => $consultationSession->getAddress(),
                "participant" => [
                    "id" => $consultationSession->getParticipant()->getId(),
                    "client" => $this->arrayDataOfClient($consultationSession->getParticipant()->getClientParticipant()),
                    "user" => $this->arrayDataOfUser($consultationSession->getParticipant()->getUserParticipant()),
                    "team" => $this->arrayDataOfTeam($consultationSession->getParticipant()->getTeamParticipant()),
                ],
                "hasConsultantFeedback" => !empty($consultationSession->getConsultantFeedback()),
                "consultant" => [
                    "id" => $consultationSession->getConsultant()->getId(),
                    'program' => [
                        "id" => $consultationSession->getConsultant()->getProgram()->getId(),
                        "name" => $consultationSession->getConsultant()->getProgram()->getName(),
                    ],
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant)? null: [
            "id" => $clientParticipant->getClient()->getId(),
            "name" => $clientParticipant->getClient()->getFullName(),
        ];
    }
    protected function arrayDataOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant)? null: [
            "id" => $userParticipant->getUser()->getId(),
            "name" => $userParticipant->getUser()->getFullName(),
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
