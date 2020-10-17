<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use Query\{
    Application\Service\Firm\Personnel\ProgramConsultation\ViewActivityLog,
    Domain\Model\Firm\Program\Consultant\ConsultantActivityLog,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest\ConsultationRequestActivityLog,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession\ConsultationSessionActivityLog,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment\CommentActivityLog
};

class ActivityLogController extends PersonnelBaseController
{

    public function showAll($programConsultationId)
    {
        $service = $this->buildViewService();
        $consultantActivityLogs = $service->showAll(
                $this->personnelId(), $programConsultationId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($consultantActivityLogs);
        foreach ($consultantActivityLogs as $consultantActivityLog) {
            $result["list"][] = $this->arrayDataOfConsultantActivityLog ($consultantActivityLog);
        }
        return  $this->listQueryResponse($result);
    }

    protected function arrayDataOfConsultantActivityLog(ConsultantActivityLog $consultantActivityLog): array
    {
        return [
            "id" => $consultantActivityLog->getId(),
            "message" => $consultantActivityLog->getMessage(),
            "occuredTime" => $consultantActivityLog->getOccuredTimeString(),
            "consultationRequest" => $this->arrayDataOfConsultationRequest($consultantActivityLog->getConsultationRequestActivityLog()),
            "consultationSession" => $this->arrayDataOfConsultationSession($consultantActivityLog->getConsultationSessionActivityLog()),
            "comment" => $this->arrayDataOfComment($consultantActivityLog->getCommentActivityLog()),
        ];
    }

    protected function arrayDataOfConsultationRequest(?ConsultationRequestActivityLog $consultationRequestActivityLog): ?array
    {
        return empty($consultationRequestActivityLog)? null: [
            "id" => $consultationRequestActivityLog->getConsultationRequest()->getId(),
            "programConsultation" => [
                "id" => $consultationRequestActivityLog->getConsultationRequest()->getConsultant()->getId(),
            ],
        ];
    }

    protected function arrayDataOfConsultationSession(?ConsultationSessionActivityLog $consultationSessionActivityLog): ?array
    {
        return empty($consultationSessionActivityLog)? null: [
            "id" => $consultationSessionActivityLog->getConsultationSession()->getId(),
            "programConsultation" => [
                "id" => $consultationSessionActivityLog->getConsultationSession()->getConsultant()->getId(),
            ],
        ];
    }

    protected function arrayDataOfComment(?CommentActivityLog $commentActivityLog): ?array
    {
        return empty($commentActivityLog)? null: [
            "id" => $commentActivityLog->getComment()->getId(),
            "worksheet" => [
                "id" => $commentActivityLog->getComment()->getWorksheet()->getId(),
                "participant" => [
                    "id" => $commentActivityLog->getComment()->getWorksheet()->getParticipant()->getId(),
                    "program" => [
                        "id" => $commentActivityLog->getComment()->getWorksheet()->getParticipant()->getProgram()->getId(),
                    ],
                ],
            ],
        ];
    }

    protected function buildViewService()
    {
        $consultantActivityLogRepository = $this->em->getRepository(ConsultantActivityLog::class);
        return new ViewActivityLog($consultantActivityLogRepository);
    }

}
