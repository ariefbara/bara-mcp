<?php

namespace App\Http\Controllers\User;

use Query\ {
    Application\Service\User\ViewUserNotification,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest\ConsultationRequestNotification,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession\ConsultationSessionNotification,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment\CommentNotification,
    Domain\Model\User\UserNotificationRecipient
};

class NotificationController extends UserBaseController
{
    public function showAll()
    {
        $service = $this->buildViewService();
        $readStatus = $this->filterBooleanOfQueryRequest("readStatus");
        $userNotifications = $service->showAll($this->userId(), $this->getPage(), $this->getPageSize(), $readStatus);
        
        $result = [];
        $result["total"] = count($userNotifications);
        foreach ($userNotifications as $userNotification) {
            $result["list"][] = $this->arrayDataOfUserNotification($userNotification);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfUserNotification(UserNotificationRecipient $userNotification): array
    {
        return [
            "id" => $userNotification->getId(),
            "message" => $userNotification->getMessage(),
            "notifiedTime" => $userNotification->getNotifiedTimeString(),
            "read" => $userNotification->isRead(),
            "consultationRequest" => $this->arrayDataOfConsultationRequest($userNotification->getConsultationRequestNotification()),
            "consultationSession" => $this->arrayDataOfConsultationSession($userNotification->getConsultationSessionNotification()),
            "comment" => $this->arrayDataOfComment($userNotification->getCommentNotification()),
        ];
    }
    protected function arrayDataOfConsultationRequest(?ConsultationRequestNotification $consultationRequestNotification): ?array
    {
        return !isset($consultationRequestNotification)? null: [
            "id" => $consultationRequestNotification->getConsultationRequest()->getId(),
        ];
    }
    protected function arrayDataOfConsultationSession(?ConsultationSessionNotification $consultationSessionNotification): ?array
    {
        return !isset($consultationSessionNotification)? null: [
            "id" => $consultationSessionNotification->getConsultationSession()->getId(),
        ];
    }
    protected function arrayDataOfComment(?CommentNotification $commentNotification): ?array
    {
        return !isset($commentNotification)? null: [
            "id" => $commentNotification->getComment()->getId(),
        ];
    }
    protected function buildViewService()
    {
        $userNotificationRepository = $this->em->getRepository(UserNotificationRecipient::class);
        return new ViewUserNotification($userNotificationRepository);
    }
}
