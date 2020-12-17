<?php

namespace Tests\Controllers;

class NotificationChecker extends ControllerTestCase
{
    public function __construct()
    {
        parent::setUp();
    }
    
    public function checkNotificationExist(string $message): self
    {
        $notificationEntry = [
            "message" => $message,
        ];
        $this->seeInDatabase("Notification", $notificationEntry);
        return $this;
    }
    
    public function checkClientNotificationExist(string $clientId): self
    {
        $clientNotificationRecipientEntry = [
            "Client_id" => $clientId,
        ];
        $this->seeInDatabase("ClientNotificationRecipient", $clientNotificationRecipientEntry);
        return $this;
    }
    
    public function checkManagerNotificationExist(string $managerId): self
    {
        $managerNotificationRecipientEntry = [
            "Manager_id" => $managerId,
        ];
        $this->seeInDatabase("ManagerNotificationRecipient", $managerNotificationRecipientEntry);
        return $this;
    }
    
    public function checkPersonnelNotificationExist(string $personnelId): self
    {
        $personnelNotificationRecipientEntry = [
            "Personnel_id" => $personnelId,
        ];
        $this->seeInDatabase("PersonnelNotificationRecipient", $personnelNotificationRecipientEntry);
        return $this;
    }
    
    public function checkUserNotificationExist(string $userId): self
    {
        $userNotificationRecipientEntry = [
            "User_id" => $userId,
        ];
        $this->seeInDatabase("UserNotificationRecipient", $userNotificationRecipientEntry);
        return $this;
    }
}
