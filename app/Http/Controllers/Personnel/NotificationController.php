<?php

namespace App\Http\Controllers\Personnel;

use Query\ {
    Application\Service\Firm\Personnel\ViewPersonnelNotification,
    Domain\Model\Firm\Personnel\PersonnelNotificationRecipient,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest\ConsultationRequestNotification,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession\ConsultationSessionNotification,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment\CommentNotification
};

class NotificationController extends PersonnelBaseController
{
    public function showAll()
    {
        $service = $this->buildViewService();
        $readStatus = $this->filterBooleanOfQueryRequest("readStatus");
        $personnelNotifications = $service->showAll($this->personnelId(), $this->getPage(), $this->getPageSize(), $readStatus);
        
        $result = [];
        $result["total"] = count($personnelNotifications);
        foreach ($personnelNotifications as $personnelNotification) {
            $result["list"][] = $this->arrayDataOfPersonnelNotification($personnelNotification);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfPersonnelNotification(PersonnelNotificationRecipient $personnelNotification): array
    {
        return [
            "id" => $personnelNotification->getId(),
            "message" => $personnelNotification->getMessage(),
            "notifiedTime" => $personnelNotification->getNotifiedTimeString(),
            "read" => $personnelNotification->isRead(),
            "consultationRequest" => $this->arrayDataOfConsultationRequest($personnelNotification->getConsultationRequestNotification()),
            "consultationSession" => $this->arrayDataOfConsultationSession($personnelNotification->getConsultationSessionNotification()),
            "comment" => $this->arrayDataOfComment($personnelNotification->getCommentNotification()),
        ];
    }
    protected function arrayDataOfConsultationRequest(?ConsultationRequestNotification $consultationRequestNotification): ?array
    {
        return empty($consultationRequestNotification)? null: [
            "id" => $consultationRequestNotification->getConsultationRequest()->getId(),
            "programConsultation" => [
                "id" => $consultationRequestNotification->getConsultationRequest()->getConsultant()->getId(),
            ],
        ];
    }
    protected function arrayDataOfConsultationSession(?ConsultationSessionNotification $consultationSessionNotification): ?array
    {
        return empty($consultationSessionNotification)? null: [
            "id" => $consultationSessionNotification->getConsultationSession()->getId(),
            "programConsultation" => [
                "id" => $consultationSessionNotification->getConsultationSession()->getConsultant()->getId(),
            ],
        ];
    }
    protected function arrayDataOfComment(?CommentNotification $commentNotification): ?array
    {
        return empty($commentNotification)? null: [
            "id" => $commentNotification->getComment()->getId(),
            "worksheet" => [
                "id" => $commentNotification->getComment()->getWorksheet()->getId(),
                "participant" => [
                    "id" => $commentNotification->getComment()->getWorksheet()->getParticipant()->getId(),
                    "program" => [
                        "id" => $commentNotification->getComment()->getWorksheet()->getParticipant()->getProgram()->getId(),
                    ],
                ],
            ],
        ];
    }
    protected function buildViewService()
    {
        $personnelNotificationRepository = $this->em->getRepository(PersonnelNotificationRecipient::class);
        return new ViewPersonnelNotification($personnelNotificationRepository);
    }
}
