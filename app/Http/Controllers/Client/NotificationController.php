<?php

namespace App\Http\Controllers\Client;

use Query\ {
    Application\Service\Firm\Client\ViewClientNotification,
    Domain\Model\Firm\Client\ClientNotificationRecipient,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest\ConsultationRequestNotification,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession\ConsultationSessionNotification,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment\CommentNotification
};

class NotificationController extends ClientBaseController
{

    public function showAll()
    {
        $service = $this->buildViewService();
        $readStatus = $this->filterBooleanOfQueryRequest("readStatus");
        $clientNotifications = $service->showAll(
                $this->firmId(), $this->clientId(), $this->getPage(), $this->getPageSize(), $readStatus);
        
        $result = [];
        $result["total"] = count($clientNotifications);
        foreach ($clientNotifications as $clientNotification) {
            $result["list"][] = $this->arrayDataOfClientNotification($clientNotification);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfClientNotification(ClientNotificationRecipient $clientNotification): array
    {
        return [
            "id" => $clientNotification->getId(),
            "message" => $clientNotification->getMessage(),
            "notifiedTime" => $clientNotification->getNotifiedTimeString(),
            "read" => $clientNotification->isRead(),
            "consultationRequest" => $this->arrayDataOfConsultationRequest($clientNotification->getConsultationRequestNotification()),
            "consultationSession" => $this->arrayDataOfConsultationSession($clientNotification->getConsultationSessionNotification()),
            "comment" => $this->arrayDataOfComment($clientNotification->getCommentNotification()),
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
        $clientNotificationRecipientReposiotry = $this->em->getRepository(ClientNotificationRecipient::class);
        return new ViewClientNotification($clientNotificationRecipientReposiotry);
    }

}
