<?php

namespace App\Http\Controllers\Personnel\Coordinator;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use Query\Application\Service\Firm\Personnel\ViewCoordinatorNotification;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession\ConsultationSessionNotification;
use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorNotificationRecipient;

class NotificationController extends PersonnelBaseController
{
    public function showAll($coordinatorId)
    {
        $notifications = $this->buildViewService()->showAll(
                $this->firmId(), $this->personnelId(), $coordinatorId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($notifications);
        foreach ($notifications as $notification) {
            $result["list"][] = $this->arrayDataOfNotification($notification);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfNotification(CoordinatorNotificationRecipient $notification): array
    {
        return [
            "id" => $notification->getId(),
            "message" => $notification->getMessage(),
            "notifiedTime" => $notification->getNotifiedTimeString(),
            "read" => $notification->isRead(),
            "consultationSession" => $this->arrayDataOfConsultationSession($notification->getConsultationSessionNotification()),
        ];
    }
    protected function arrayDataOfConsultationSession(?ConsultationSessionNotification $consultationSessionNotification): ?array
    {
        return empty($consultationSessionNotification)? null: [
            "id" => $consultationSessionNotification->getConsultationSession()->getId(),
        ];
    }
    protected function buildViewService()
    {
        $coordinatorNotificationRepository = $this->em->getRepository(CoordinatorNotificationRecipient::class);
        return new ViewCoordinatorNotification($coordinatorNotificationRepository);
    }
}
