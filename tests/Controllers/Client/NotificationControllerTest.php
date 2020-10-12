<?php

namespace Tests\Controllers\Client;

use Tests\Controllers\RecordPreparation\ {
    Firm\Client\RecordOfClientNotificationRecipient,
    Firm\Program\Participant\ConsultationRequest\RecordOfConsultationRequestNotification,
    Firm\Program\Participant\ConsultationSession\RecordOfConsultationSessionNotification,
    Firm\Program\Participant\RecordOfConsultationRequest,
    Firm\Program\Participant\RecordOfConsultationSession,
    Firm\Program\Participant\RecordOfWorksheet,
    Firm\Program\Participant\Worksheet\Comment\RecordOfCommentNotification,
    Firm\Program\Participant\Worksheet\RecordOfComment,
    Firm\Program\RecordOfConsultant,
    Firm\Program\RecordOfConsultationSetup,
    Firm\Program\RecordOfMission,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfFeedbackForm,
    Firm\RecordOfPersonnel,
    Firm\RecordOfProgram,
    Firm\RecordOfWorksheetForm,
    Shared\RecordOfForm,
    Shared\RecordOfFormRecord,
    Shared\RecordOfNotification
};

class NotificationControllerTest extends ClientTestCase
{
    protected $notificationUri;
    protected $clientNotification_consultationRequest;
    protected $clientNotificationOne_consultationSession;
    protected $clientNotificationTwo_comment;
    protected $clientNotificationThree_consultationRequest_read;
    
    protected $consultationRequestNotification;
    protected $consultationRequestNotificationThree_read;
    protected $consultationSessionNotificationOne;
    protected $commentNotificationTwo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationUri = $this->clientUri . "/notifications";
        
        $this->connection->table("Notification")->truncate();
        $this->connection->table("ClientNotificationRecipient")->truncate();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ConsultationSetup")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("ConsultationRequest")->truncate();
        $this->connection->table("ConsultationSession")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("WorksheetForm")->truncate();
        $this->connection->table("Mission")->truncate();
        $this->connection->table("Worksheet")->truncate();
        $this->connection->table("Comment")->truncate();
        $this->connection->table("ConsultationRequestNotification")->truncate();
        $this->connection->table("ConsultationSessionNotification")->truncate();
        $this->connection->table("CommentNotification")->truncate();
        
        $notification = new RecordOfNotification(0);
        $notificationOne = new RecordOfNotification(1);
        $notificationTwo = new RecordOfNotification(2);
        $notificationThree = new RecordOfNotification(3);
        $this->connection->table("Notification")->insert($notification->toArrayForDbEntry());
        $this->connection->table("Notification")->insert($notificationOne->toArrayForDbEntry());
        $this->connection->table("Notification")->insert($notificationTwo->toArrayForDbEntry());
        $this->connection->table("Notification")->insert($notificationThree->toArrayForDbEntry());
        
        $this->clientNotification_consultationRequest = new RecordOfClientNotificationRecipient($this->client, $notification);
        $this->clientNotificationOne_consultationSession = new RecordOfClientNotificationRecipient($this->client, $notificationOne);
        $this->clientNotificationTwo_comment = new RecordOfClientNotificationRecipient($this->client, $notificationTwo);
        $this->clientNotificationThree_consultationRequest_read = new RecordOfClientNotificationRecipient($this->client, $notificationThree);
        $this->clientNotificationThree_consultationRequest_read->readStatus = true;
        $this->connection->table("ClientNotificationRecipient")->insert($this->clientNotification_consultationRequest->toArrayForDbEntry());
        $this->connection->table("ClientNotificationRecipient")->insert($this->clientNotificationOne_consultationSession->toArrayForDbEntry());
        $this->connection->table("ClientNotificationRecipient")->insert($this->clientNotificationTwo_comment->toArrayForDbEntry());
        $this->connection->table("ClientNotificationRecipient")->insert($this->clientNotificationThree_consultationRequest_read->toArrayForDbEntry());
        
        $firm = $this->client->firm;
        
        $program = new RecordOfProgram($firm, 0);
        $this->connection->table("Program")->insert($program->toArrayForDbEntry());
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $feedbackForm = new RecordOfFeedbackForm($firm, $form);
        $this->connection->table("FeedbackForm")->insert($feedbackForm->toArrayForDbEntry());
        
        $consultationSetup = new RecordOfConsultationSetup($program, $feedbackForm, $feedbackForm, 0);
        $this->connection->table("ConsultationSetup")->insert($consultationSetup->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($program, 0);
        $this->connection->table("Participant")->insert($participant->toArrayForDbEntry());
        
        $personnel = new RecordOfPersonnel($firm, 0);
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());
        
        $consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->connection->table("Consultant")->insert($consultant->toArrayForDbEntry());
        
        $consultationRequest = new RecordOfConsultationRequest($consultationSetup, $participant, $consultant, 0);
        $this->connection->table("ConsultationRequest")->insert($consultationRequest->toArrayForDbEntry());
        
        $consultationSession = new RecordOfConsultationSession($consultationSetup, $participant, $consultant, 0);
        $this->connection->table("ConsultationSession")->insert($consultationSession->toArrayForDbEntry());
        
        $formRecord = new RecordOfFormRecord($form, 0);
        $this->connection->table("FormRecord")->insert($formRecord->toArrayForDbEntry());
        
        $worksheetForm = new RecordOfWorksheetForm($firm, $form);
        $this->connection->table("WorksheetForm")->insert($worksheetForm->toArrayForDbEntry());
        
        $mission = new RecordOfMission($program, $worksheetForm, 0, null);
        $this->connection->table("Mission")->insert($mission->toArrayForDbEntry());
        
        $worksheet = new RecordOfWorksheet($participant, $formRecord, $mission, 0);
        $this->connection->table("Worksheet")->insert($worksheet->toArrayForDbEntry());
        
        $comment = new RecordOfComment($worksheet, 0);
        $this->connection->table("Comment")->insert($comment->toArrayForDbEntry());
        
        $this->consultationRequestNotification = new RecordOfConsultationRequestNotification($consultationRequest, $notification);
        $this->consultationRequestNotificationThree_read = new RecordOfConsultationRequestNotification($consultationRequest, $notificationThree);
        $this->connection->table("ConsultationRequestNotification")->insert($this->consultationRequestNotification->toArrayForDbEntry());
        $this->connection->table("ConsultationRequestNotification")->insert($this->consultationRequestNotificationThree_read->toArrayForDbEntry());
        
        $this->consultationSessionNotificationOne = new RecordOfConsultationSessionNotification($consultationSession, $notificationOne);
        $this->connection->table("ConsultationSessionNotification")->insert($this->consultationSessionNotificationOne->toArrayForDbEntry());
        
        $this->commentNotificationTwo = new RecordOfCommentNotification($comment, $notificationTwo);
        $this->connection->table("CommentNotification")->insert($this->commentNotificationTwo->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Notification")->truncate();
        $this->connection->table("ClientNotificationRecipient")->truncate();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ConsultationSetup")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("ConsultationRequest")->truncate();
        $this->connection->table("ConsultationSession")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("WorksheetForm")->truncate();
        $this->connection->table("Mission")->truncate();
        $this->connection->table("Worksheet")->truncate();
        $this->connection->table("Comment")->truncate();
        $this->connection->table("ConsultationRequestNotification")->truncate();
        $this->connection->table("ConsultationSessionNotification")->truncate();
        $this->connection->table("CommentNotification")->truncate();
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 4,
            "list" => [
                [
                    "id" => $this->clientNotification_consultationRequest->id,
                    "message" => $this->clientNotification_consultationRequest->notification->message,
                    "notifiedTime" => $this->clientNotification_consultationRequest->notifiedTime,
                    "read" => $this->clientNotification_consultationRequest->readStatus,
                    "consultationRequest" => [
                        "id" => $this->consultationRequestNotification->consultationRequest->id,
                    ],
                    "consultationSession" => null,
                    "comment" => null,
                ],
                [
                    "id" => $this->clientNotificationOne_consultationSession->id,
                    "message" => $this->clientNotificationOne_consultationSession->notification->message,
                    "notifiedTime" => $this->clientNotificationOne_consultationSession->notifiedTime,
                    "read" => $this->clientNotificationOne_consultationSession->readStatus,
                    "consultationRequest" => null,
                    "consultationSession" => [
                        "id" => $this->consultationSessionNotificationOne->consultationSession->id,
                    ],
                    "comment" => null,
                ],
                [
                    "id" => $this->clientNotificationTwo_comment->id,
                    "message" => $this->clientNotificationTwo_comment->notification->message,
                    "notifiedTime" => $this->clientNotificationTwo_comment->notifiedTime,
                    "read" => $this->clientNotificationTwo_comment->readStatus,
                    "consultationRequest" => null,
                    "consultationSession" => null,
                    "comment" => [
                        "id" => $this->commentNotificationTwo->comment->id,
                    ],
                ],
                [
                    "id" => $this->clientNotificationThree_consultationRequest_read->id,
                    "message" => $this->clientNotificationThree_consultationRequest_read->notification->message,
                    "notifiedTime" => $this->clientNotificationThree_consultationRequest_read->notifiedTime,
                    "read" => $this->clientNotificationThree_consultationRequest_read->readStatus,
                    "consultationRequest" => [
                        "id" => $this->consultationRequestNotificationThree_read->consultationRequest->id,
                    ],
                    "consultationSession" => null,
                    "comment" => null,
                ],
            ],
        ];
        $this->get($this->notificationUri, $this->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_containQueryReadStatus()
    {
        $response = [
            "total" => 3,
            "list" => [
                [
                    "id" => $this->clientNotification_consultationRequest->id,
                    "message" => $this->clientNotification_consultationRequest->notification->message,
                    "notifiedTime" => $this->clientNotification_consultationRequest->notifiedTime,
                    "read" => $this->clientNotification_consultationRequest->readStatus,
                    "consultationRequest" => [
                        "id" => $this->consultationRequestNotification->consultationRequest->id,
                    ],
                    "consultationSession" => null,
                    "comment" => null,
                ],
                [
                    "id" => $this->clientNotificationOne_consultationSession->id,
                    "message" => $this->clientNotificationOne_consultationSession->notification->message,
                    "notifiedTime" => $this->clientNotificationOne_consultationSession->notifiedTime,
                    "read" => $this->clientNotificationOne_consultationSession->readStatus,
                    "consultationRequest" => null,
                    "consultationSession" => [
                        "id" => $this->consultationSessionNotificationOne->consultationSession->id,
                    ],
                    "comment" => null,
                ],
                [
                    "id" => $this->clientNotificationTwo_comment->id,
                    "message" => $this->clientNotificationTwo_comment->notification->message,
                    "notifiedTime" => $this->clientNotificationTwo_comment->notifiedTime,
                    "read" => $this->clientNotificationTwo_comment->readStatus,
                    "consultationRequest" => null,
                    "consultationSession" => null,
                    "comment" => [
                        "id" => $this->commentNotificationTwo->comment->id,
                    ],
                ],
            ],
        ];
        $uri = $this->notificationUri . "?readStatus=false";
        $this->get($uri, $this->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
