<?php

namespace Tests\Controllers\Personnel\Coordinator;

use Tests\Controllers\RecordPreparation\Firm\Program\Coordinator\RecordOfCoordinatorNotificationRecipient;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\ConsultationSession\RecordOfConsultationSessionNotification;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfConsultationSession;

class NotificationControllerTest extends CoordinatorTestCase
{
    protected $coordinatorNotificationOne;
    protected $coordinatorNotificationTwo;
    protected $consultationSessionOne;
    protected $consultationSessionTwo;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connection->table("Notification")->truncate();
        $this->connection->table("CoordinatorNotificationRecipient")->truncate();
        $this->connection->table("ConsultationSession")->truncate();
        $this->connection->table("ConsultationSessionNotification")->truncate();
        
        $this->coordinatorNotificationOne = new RecordOfCoordinatorNotificationRecipient($this->coordinator, null, 1);
        $this->coordinatorNotificationTwo = new RecordOfCoordinatorNotificationRecipient($this->coordinator, null, 2);
        $this->connection->table("Notification")->insert($this->coordinatorNotificationOne->notification->toArrayForDbEntry());
        $this->connection->table("Notification")->insert($this->coordinatorNotificationTwo->notification->toArrayForDbEntry());
        $this->connection->table("CoordinatorNotificationRecipient")->insert($this->coordinatorNotificationOne->toArrayForDbEntry());
        $this->connection->table("CoordinatorNotificationRecipient")->insert($this->coordinatorNotificationTwo->toArrayForDbEntry());
        
        $this->consultationSessionOne = new RecordOfConsultationSession(null, null, null, 1);
        $this->consultationSessionTwo = new RecordOfConsultationSession(null, null, null, 2);
        $this->connection->table("ConsultationSession")->insert($this->consultationSessionOne->toArrayForDbEntry());
        $this->connection->table("ConsultationSession")->insert($this->consultationSessionTwo->toArrayForDbEntry());
        
        $consultationSessionNotificationOne = new RecordOfConsultationSessionNotification(
                $this->consultationSessionOne, $this->coordinatorNotificationOne->notification);
        $consultationSessionNotificationTwo = new RecordOfConsultationSessionNotification(
                $this->consultationSessionTwo, $this->coordinatorNotificationTwo->notification);
        $this->connection->table("ConsultationSessionNotification")->insert($consultationSessionNotificationOne->toArrayForDbEntry());
        $this->connection->table("ConsultationSessionNotification")->insert($consultationSessionNotificationTwo->toArrayForDbEntry());
    }
    
    public function test_showAll_200()
    {
$this->disableExceptionHandling();
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->coordinatorNotificationOne->id,
                    "message" => $this->coordinatorNotificationOne->notification->message,
                    "notifiedTime" => $this->coordinatorNotificationOne->notifiedTime,
                    "read" => $this->coordinatorNotificationOne->readStatus,
                    "consultationSession" => [
                        "id" => $this->consultationSessionOne->id,
                    ],
                ],
                [
                    "id" => $this->coordinatorNotificationTwo->id,
                    "message" => $this->coordinatorNotificationTwo->notification->message,
                    "notifiedTime" => $this->coordinatorNotificationTwo->notifiedTime,
                    "read" => $this->coordinatorNotificationTwo->readStatus,
                    "consultationSession" => [
                        "id" => $this->consultationSessionTwo->id,
                    ],
                ],
            ],
        ];
        
        $uri = $this->coordinatorUri . "/{$this->coordinator->id}/notifications";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
}
