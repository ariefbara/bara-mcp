<?php

namespace App\Http\Controllers\Client;

use Query\Application\Service\Firm\Client\AsTeamMember\ViewTeamMemberActivityLog;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest\ConsultationRequestActivityLog;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession\ConsultationSessionActivityLog;
use Query\Domain\Model\Firm\Program\Participant\ViewLearningMaterialActivityLog;
use Query\Domain\Model\Firm\Program\Participant\Worksheet\Comment\CommentActivityLog;
use Query\Domain\Model\Firm\Program\Participant\Worksheet\WorksheetActivityLog;
use Query\Domain\Model\Firm\Team\Member;
use Query\Domain\Model\Firm\Team\Member\TeamMemberActivityLog;

class TeamMemberActivityLogController extends ClientBaseController
{

    public function showAll($memberId)
    {
        $teamMemberActivityLogs = $this->buildViewService()
                ->showAll($this->firmId(), $this->clientId(), $memberId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($teamMemberActivityLogs);
        foreach ($teamMemberActivityLogs as $teamMemberActivityLog) {
            $result['list'][] = $this->arrayDataOfTeamMemberActivityLog($teamMemberActivityLog);
        }
        return $this->listQueryResponse($result);
    }

    public function show($memberId, $id)
    {
        $teamMemberActivityLog = $this->buildViewService()->showById($this->firmId(), $this->clientId(), $memberId, $id);
        return $this->singleQueryResponse($this->arrayDataOfTeamMemberActivityLog($teamMemberActivityLog));
    }

    protected function arrayDataOfTeamMemberActivityLog(TeamMemberActivityLog $teamMemberActivityLog): array
    {
        return [
            'id' => $teamMemberActivityLog->getId(),
            'message' => $teamMemberActivityLog->getMessage(),
            'occuredTime' => $teamMemberActivityLog->getOccuredTimeString(),
            'consultationRequest' => $this->arrayDataOfConsultationRequest($teamMemberActivityLog->getConsultationRequestActivityLog()),
            'consultationSession' => $this->arrayDataOfConsultationSession($teamMemberActivityLog->getConsultationSessionActivityLog()),
            'worksheet' => $this->arrayDataOfWorksheet($teamMemberActivityLog->getWorksheetActivityLog()),
            'comment' => $this->arrayDataOfComment($teamMemberActivityLog->getCommentActivityLog()),
            'learningMaterial' => $this->arrayDataOfLearningMaterial($teamMemberActivityLog->getViewLearningMaterialActivityLog()),
        ];
    }
    protected function arrayDataOfConsultationRequest(?ConsultationRequestActivityLog $consultationRequestActivityLog): ?array
    {
        return empty($consultationRequestActivityLog) ? null : [
            "id" => $consultationRequestActivityLog->getConsultationRequest()->getId(),
            "startTime" => $consultationRequestActivityLog->getConsultationRequest()->getStartTimeString(),
            "endTime" => $consultationRequestActivityLog->getConsultationRequest()->getEndTimeString(),
            "consultant" => [
                "id" => $consultationRequestActivityLog->getConsultationRequest()->getConsultant()->getId(),
                "personnel" => [
                    "id" => $consultationRequestActivityLog->getConsultationRequest()->getConsultant()->getPersonnel()->getId(),
                    "name" => $consultationRequestActivityLog->getConsultationRequest()->getConsultant()->getPersonnel()->getName(),
                ],
            ],
        ];
    }
    protected function arrayDataOfConsultationSession(?ConsultationSessionActivityLog $consultationSessionActivityLog): ?array
    {
        return empty($consultationSessionActivityLog)? null: [
            "id" => $consultationSessionActivityLog->getConsultationSession()->getId(),
            "startTime" => $consultationSessionActivityLog->getConsultationSession()->getStartTime(),
            "endTime" => $consultationSessionActivityLog->getConsultationSession()->getEndTime(),
            "consultant" => [
                "id" => $consultationSessionActivityLog->getConsultationSession()->getConsultant()->getId(),
                "personnel" => [
                    "id" => $consultationSessionActivityLog->getConsultationSession()->getConsultant()->getPersonnel()->getId(),
                    "name" => $consultationSessionActivityLog->getConsultationSession()->getConsultant()->getPersonnel()->getName(),
                ],
            ],
        ];
    }
    protected function arrayDataOfWorksheet(?WorksheetActivityLog $worksheetActivityLog): ?array
    {
        return empty($worksheetActivityLog)? null: [
            "id" => $worksheetActivityLog->getWorksheet()->getId(),
            "name" => $worksheetActivityLog->getWorksheet()->getName(),
            "mission" => [
                "id" => $worksheetActivityLog->getWorksheet()->getMission()->getId(),
                "name" => $worksheetActivityLog->getWorksheet()->getMission()->getName(),
                "position" => $worksheetActivityLog->getWorksheet()->getMission()->getPosition(),
                
            ],
        ];
    }
    protected function arrayDataOfComment(?CommentActivityLog $commentActivityLog): ?array
    {
        return empty($commentActivityLog)? null: [
            "id" => $commentActivityLog->getComment()->getId(),
            "worksheet" => [
                "id" => $commentActivityLog->getComment()->getWorksheet()->getId(),
                "name" => $commentActivityLog->getComment()->getWorksheet()->getName(),
            ],
        ];
    }
    protected function arrayDataOfLearningMaterial(?ViewLearningMaterialActivityLog $viewLearingMaterialActivityLog): ?array
    {
        return empty($viewLearingMaterialActivityLog)? null: [
           "id" => $viewLearingMaterialActivityLog->getLearningMaterial()->getId(),
           "name" => $viewLearingMaterialActivityLog->getLearningMaterial()->getName(),
            "mission" => [
               "id" => $viewLearingMaterialActivityLog->getLearningMaterial()->getMission()->getId(),
               "name" => $viewLearingMaterialActivityLog->getLearningMaterial()->getMission()->getName(),
            ],
        ];
    }

    protected function buildViewService()
    {
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $teamMemberActivityLogRepository = $this->em->getRepository(TeamMemberActivityLog::class);
        return new ViewTeamMemberActivityLog($teamMemberRepository, $teamMemberActivityLogRepository);
    }

}
