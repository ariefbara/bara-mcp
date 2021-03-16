<?php

namespace App\Http\Controllers\User\ProgramParticipation;

use App\Http\Controllers\User\UserBaseController;
use Query\Application\Service\User\AsProgramParticipant\ViewActivityLog as ViewActivityLog2;
use Query\Application\Service\User\ProgramParticipation\ViewActivityLog;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest\ConsultationRequestActivityLog;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession\ConsultationSessionActivityLog;
use Query\Domain\Model\Firm\Program\Participant\ViewLearningMaterialActivityLog;
use Query\Domain\Model\Firm\Program\Participant\Worksheet\Comment\CommentActivityLog;
use Query\Domain\Model\Firm\Program\Participant\Worksheet\WorksheetActivityLog;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\SharedModel\ActivityLog;

class ActivityLogController extends UserBaseController
{
    public function showAll($programParticipationId)
    {
        $service = $this->buildViewService();
        $activityLogs = $service->showAll($this->userId(), $programParticipationId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($activityLogs);
        foreach ($activityLogs as $activityLog) {
            $result["list"][] = $this->arrayDataOfActivityLog($activityLog);
        }
        return $this->listQueryResponse($result);
    }
    
    public function showSelfActivityLogs($programParticipationId)
    {
        $activityLogs = $this->buildViewActivityLog()->showSelfActivityLog(
                $this->userId(), $programParticipationId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($activityLogs);
        foreach ($activityLogs as $activityLog) {
            $result["list"][] = $this->arrayDataOfActivityLog($activityLog);
        }
        return $this->listQueryResponse($result);
    }
    public function showSharedActivityLogs($programParticipationId)
    {
        $activityLogs = $this->buildViewActivityLog()->showSharedActivityLog(
                $this->userId(), $programParticipationId, $this->getPage(), $this->getPageSize());
        
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
            "consultationRequest" => $this->arrayDataOfConsultationRequest($activityLog->getConsultationRequestActivityLog()),
            "consultationSession" => $this->arrayDataOfConsultationSession($activityLog->getConsultationSessionActivityLog()),
            "worksheet" => $this->arrayDataOfWorksheet($activityLog->getWorksheetActivityLog()),
            "comment" => $this->arrayDataOfComment($activityLog->getCommentActivityLog()),
            "learningMaterial" => $this->arrayDataOfLearningMaterial($activityLog->getViewLearningMaterialActivityLog()),
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
        $activityLogRepository = $this->em->getRepository(ActivityLog::class);
        return new ViewActivityLog($activityLogRepository);
    }
    
    protected function buildViewActivityLog()
    {
        $userParticipantRepository = $this->em->getRepository(UserParticipant::class);
        $activityLogRepository = $this->em->getRepository(ActivityLog::class);
        return new ViewActivityLog2($userParticipantRepository, $activityLogRepository);
    }
}
