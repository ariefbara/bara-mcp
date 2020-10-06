<?php

namespace App\Http\Controllers\Client\TeamMembership\ProgramParticipation;

use App\Http\Controllers\Client\TeamMembership\TeamMembershipBaseController;
use Query\ {
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\ViewActivityLog,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest\ConsultationRequestActivityLog,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession\ConsultationSessionActivityLog,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment\CommentActivityLog,
    Domain\Model\Firm\Program\Participant\Worksheet\WorksheetActivityLog,
    Domain\SharedModel\ActivityLog
};

class ActivityLogController extends TeamMembershipBaseController
{

    public function showAll($teamMembershipId, $teamProgramParticipationId)
    {
        $this->authorizeActiveTeamMember($teamMembershipId);
        $service = $this->buildViewService();
        $activityLogs = $service->showAll(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId, $this->getPage(),
                $this->getPageSize());
        
        $result = [];
        $result["total"] = count($activityLogs);
        foreach ($activityLogs as $activityLog) {
            $result["list"][] = $this->arrayDataOfActivityLog($activityLog);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfActivityLog(ActivityLog $activityLog): array
    {
        return [
            "id" => $activityLog->getId(),
            "message" => $activityLog->getMessage(),
            "occuredTime" => $activityLog->getOccuredTimeString(),
            "teamMember" => [
                "id" => $activityLog->getTeamMemberActivityLog()->getMember()->getId(),
                "client" => [
                    "id" => $activityLog->getTeamMemberActivityLog()->getMember()->getClient()->getId(),
                    "name" => $activityLog->getTeamMemberActivityLog()->getMember()->getClient()->getFullName(),
                ],
            ],
            "consultationRequest" => $this->arrayDataOfConsultationRequest($activityLog->getConsultationRequestActivityLog()),
            "consultationSession" => $this->arrayDataOfConsultationSession($activityLog->getConsultationSessionActivityLog()),
            "worksheet" => $this->arrayDataOfWorksheet($activityLog->getWorksheetActivityLog()),
            "comment" => $this->arrayDataOfComment($activityLog->getCommentActivityLog()),
        ];
    }

    protected function arrayDataOfConsultationRequest(?ConsultationRequestActivityLog $consultationRequestActivityLog): ?array
    {
        return empty($consultationRequestActivityLog) ? null : [
            "id" => $consultationRequestActivityLog->getConsultationRequest()->getId(),
        ];
    }
    protected function arrayDataOfConsultationSession(?ConsultationSessionActivityLog $consultationSessionActivityLog): ?array
    {
        return empty($consultationSessionActivityLog)? null: [
            "id" => $consultationSessionActivityLog->getConsultationSession()->getId(),
        ];
    }
    protected function arrayDataOfWorksheet(?WorksheetActivityLog $worksheetActivityLog): ?array
    {
        return empty($worksheetActivityLog)? null: [
            "id" => $worksheetActivityLog->getWorksheet()->getId(),
        ];
    }
    protected function arrayDataOfComment(?CommentActivityLog $commentActivityLog): ?array
    {
        return empty($commentActivityLog)? null: [
            "id" => $commentActivityLog->getComment()->getId(),
            "worksheet" => [
                "id" => $commentActivityLog->getComment()->getWorksheet()->getId(),
            ],
        ];
    }

    protected function buildViewService()
    {
        $activityLogRepository = $this->em->getRepository(ActivityLog::class);
        return new ViewActivityLog($activityLogRepository);
    }

}
