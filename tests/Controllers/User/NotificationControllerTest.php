<?php

namespace Tests\Controllers\User;

use Tests\Controllers\RecordPreparation\ {
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
    RecordOfFirm,
    Shared\RecordOfForm,
    Shared\RecordOfFormRecord,
    Shared\RecordOfNotification,
    User\RecordOfUserNotificationRecipient
};

class NotificationControllerTest extends UserTestCase
{
    protected $notificationUri;
    protected $userNotification_consultationRequest;
    protected $userNotificationOne_consultationSession;
    protected $userNotificationTwo_comment;
    protected $userNotificationThree_consultationRequest_read;
    
    protected $consultationRequestNotification;
    protected $consultationRequestNotificationThree_read;
    protected $consultationSessionNotificationOne;
    protected $commentNotificationTwo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationUri = $this->userUri . "/notifications";
        
        $this->connection->table("Notification")->truncate();
        $this->connection->table("UserNotificationRecipient")->truncate();
        $this->connection->table("ConsultationRequestNotification")->truncate();
        $this->connection->table("ConsultationSessionNotification")->truncate();
        $this->connection->table("CommentNotification")->truncate();
        
        $this->connection->table("Firm")->truncate();
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
        
        $notification = new RecordOfNotification(0);
        $notificationOne = new RecordOfNotification(1);
        $notificationTwo = new RecordOfNotification(2);
        $notificationThree = new RecordOfNotification(3);
        $this->connection->table("Notification")->insert($notification->toArrayForDbEntry());
        $this->connection->table("Notification")->insert($notificationOne->toArrayForDbEntry());
        $this->connection->table("Notification")->insert($notificationTwo->toArrayForDbEntry());
        $this->connection->table("Notification")->insert($notificationThree->toArrayForDbEntry());
        
        $this->userNotification_consultationRequest = new RecordOfUserNotificationRecipient($this->user, $notification);
        $this->userNotificationOne_consultationSession = new RecordOfUserNotificationRecipient($this->user, $notificationOne);
        $this->userNotificationTwo_comment = new RecordOfUserNotificationRecipient($this->user, $notificationTwo);
        $this->userNotificationThree_consultationRequest_read = new RecordOfUserNotificationRecipient($this->user, $notificationThree);
        $this->userNotificationThree_consultationRequest_read->readStatus = true;
        $this->connection->table("UserNotificationRecipient")->insert($this->userNotification_consultationRequest->toArrayForDbEntry());
        $this->connection->table("UserNotificationRecipient")->insert($this->userNotificationOne_consultationSession->toArrayForDbEntry());
        $this->connection->table("UserNotificationRecipient")->insert($this->userNotificationTwo_comment->toArrayForDbEntry());
        $this->connection->table("UserNotificationRecipient")->insert($this->userNotificationThree_consultationRequest_read->toArrayForDbEntry());
        
        $firm = new RecordOfFirm(0);
        $this->connection->table("Firm")->insert($firm->toArrayForDbEntry());
        
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
        $this->connection->table("UserNotificationRecipient")->truncate();
        $this->connection->table("ConsultationRequestNotification")->truncate();
        $this->connection->table("ConsultationSessionNotification")->truncate();
        $this->connection->table("CommentNotification")->truncate();
        
        $this->connection->table("Firm")->truncate();
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
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 4,
            "list" => [
                [
                    "id" => $this->userNotification_consultationRequest->id,
                    "message" => $this->userNotification_consultationRequest->notification->message,
                    "notifiedTime" => $this->userNotification_consultationRequest->notifiedTime,
                    "read" => $this->userNotification_consultationRequest->readStatus,
                    "consultationRequest" => [
                        "id" => $this->consultationRequestNotification->consultationRequest->id,
                    ],
                    "consultationSession" => null,
                    "comment" => null,
                ],
                [
                    "id" => $this->userNotificationOne_consultationSession->id,
                    "message" => $this->userNotificationOne_consultationSession->notification->message,
                    "notifiedTime" => $this->userNotificationOne_consultationSession->notifiedTime,
                    "read" => $this->userNotificationOne_consultationSession->readStatus,
                    "consultationRequest" => null,
                    "consultationSession" => [
                        "id" => $this->consultationSessionNotificationOne->consultationSession->id,
                    ],
                    "comment" => null,
                ],
                [
                    "id" => $this->userNotificationTwo_comment->id,
                    "message" => $this->userNotificationTwo_comment->notification->message,
                    "notifiedTime" => $this->userNotificationTwo_comment->notifiedTime,
                    "read" => $this->userNotificationTwo_comment->readStatus,
                    "consultationRequest" => null,
                    "consultationSession" => null,
                    "comment" => [
                        "id" => $this->commentNotificationTwo->comment->id,
                    ],
                ],
                [
                    "id" => $this->userNotificationThree_consultationRequest_read->id,
                    "message" => $this->userNotificationThree_consultationRequest_read->notification->message,
                    "notifiedTime" => $this->userNotificationThree_consultationRequest_read->notifiedTime,
                    "read" => $this->userNotificationThree_consultationRequest_read->readStatus,
                    "consultationRequest" => [
                        "id" => $this->consultationRequestNotificationThree_read->consultationRequest->id,
                    ],
                    "consultationSession" => null,
                    "comment" => null,
                ],
            ],
        ];
        $this->get($this->notificationUri, $this->user->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_containQueryReadStatus()
    {
        $response = [
            "total" => 3,
            "list" => [
                [
                    "id" => $this->userNotification_consultationRequest->id,
                    "message" => $this->userNotification_consultationRequest->notification->message,
                    "notifiedTime" => $this->userNotification_consultationRequest->notifiedTime,
                    "read" => $this->userNotification_consultationRequest->readStatus,
                    "consultationRequest" => [
                        "id" => $this->consultationRequestNotification->consultationRequest->id,
                    ],
                    "consultationSession" => null,
                    "comment" => null,
                ],
                [
                    "id" => $this->userNotificationOne_consultationSession->id,
                    "message" => $this->userNotificationOne_consultationSession->notification->message,
                    "notifiedTime" => $this->userNotificationOne_consultationSession->notifiedTime,
                    "read" => $this->userNotificationOne_consultationSession->readStatus,
                    "consultationRequest" => null,
                    "consultationSession" => [
                        "id" => $this->consultationSessionNotificationOne->consultationSession->id,
                    ],
                    "comment" => null,
                ],
                [
                    "id" => $this->userNotificationTwo_comment->id,
                    "message" => $this->userNotificationTwo_comment->notification->message,
                    "notifiedTime" => $this->userNotificationTwo_comment->notifiedTime,
                    "read" => $this->userNotificationTwo_comment->readStatus,
                    "consultationRequest" => null,
                    "consultationSession" => null,
                    "comment" => [
                        "id" => $this->commentNotificationTwo->comment->id,
                    ],
                ],
            ],
        ];
        $uri = $this->notificationUri . "?readStatus=false";
        $this->get($uri, $this->user->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
