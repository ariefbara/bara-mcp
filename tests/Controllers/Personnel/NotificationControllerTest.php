<?php

namespace Tests\Controllers\Personnel;

use Tests\Controllers\RecordPreparation\ {
    Firm\Personnel\RecordOfPersonnelNotificationRecipient,
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
    Firm\RecordOfProgram,
    Firm\RecordOfWorksheetForm,
    Shared\RecordOfForm,
    Shared\RecordOfFormRecord,
    Shared\RecordOfNotification
};

class NotificationControllerTest extends PersonnelTestCase
{

    protected $notificationUri;
    protected $personnelNotification_consultationRequest;
    protected $personnelNotificationOne_consultationSession;
    protected $personnelNotificationTwo_comment;
    protected $consultationRequest;
    protected $consultationSession;
    protected $comment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationUri = $this->personnelUri . "/notifications";
        
        $this->connection->table("Notification")->truncate();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ConsultationSetup")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("ConsultationRequest")->truncate();
        $this->connection->table("ConsultationRequestNotification")->truncate();
        $this->connection->table("ConsultationSession")->truncate();
        $this->connection->table("ConsultationSessionNotification")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("WorksheetForm")->truncate();
        $this->connection->table("Mission")->truncate();
        $this->connection->table("Worksheet")->truncate();
        $this->connection->table("Comment")->truncate();
        $this->connection->table("CommentNotification")->truncate();
        $this->connection->table("PersonnelNotificationRecipient")->truncate();

        $firm = $this->personnel->firm;

        $notification = new RecordOfNotification(0);
        $notificationOne = new RecordOfNotification(1);
        $notificationTwo = new RecordOfNotification(2);
        $this->connection->table("Notification")->insert($notification->toArrayForDbEntry());
        $this->connection->table("Notification")->insert($notificationOne->toArrayForDbEntry());
        $this->connection->table("Notification")->insert($notificationTwo->toArrayForDbEntry());

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
        
        $consultant = new RecordOfConsultant($program, $this->personnel, 0);
        $this->connection->table("Consultant")->insert($consultant->toArrayForDbEntry());
        
        $this->consultationRequest = new RecordOfConsultationRequest($consultationSetup, $participant, $consultant, 0);
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest->toArrayForDbEntry());
        
        $consultationRequestNotification = new RecordOfConsultationRequestNotification(
                $this->consultationRequest, $notification);
        $this->connection->table("ConsultationRequestNotification")->insert($consultationRequestNotification->toArrayForDbEntry());
        
        $this->consultationSession = new RecordOfConsultationSession($consultationSetup, $participant, $consultant, 0);
        $this->connection->table("ConsultationSession")->insert($this->consultationSession->toArrayForDbEntry());
        
        $consultationSessionNotification = new RecordOfConsultationSessionNotification(
                $this->consultationSession, $notificationOne);
        $this->connection->table("ConsultationSessionNotification")->insert($consultationSessionNotification->toArrayForDbEntry());
        
        $formRecord = new RecordOfFormRecord($form, 0);
        $this->connection->table("FormRecord")->insert($formRecord->toArrayForDbEntry());
        
        $worksheetForm = new RecordOfWorksheetForm($firm, $form);
        $this->connection->table("WorksheetForm")->insert($worksheetForm->toArrayForDbEntry());
        
        $mission = new RecordOfMission($program, $worksheetForm, 0, null);
        $this->connection->table("Mission")->insert($mission->toArrayForDbEntry());
        
        $worksheet = new RecordOfWorksheet($participant, $formRecord, $mission, 0);
        $this->connection->table("Worksheet")->insert($worksheet->toArrayForDbEntry());
        
        $this->comment = new RecordOfComment($worksheet, 0);
        $this->connection->table("Comment")->insert($this->comment->toArrayForDbEntry());
        
        $commentNotification = new RecordOfCommentNotification($this->comment, $notificationTwo);
        $this->connection->table("CommentNotification")->insert($commentNotification->toArrayForDbEntry());

        $this->personnelNotification_consultationRequest = new RecordOfPersonnelNotificationRecipient(
                $this->personnel, $notification);
        $this->personnelNotificationOne_consultationSession = new RecordOfPersonnelNotificationRecipient(
                $this->personnel, $notificationOne);
        $this->personnelNotificationTwo_comment = new RecordOfPersonnelNotificationRecipient(
                $this->personnel, $notificationTwo);
        $this->personnelNotificationTwo_comment->readStatus = true;
        $this->connection->table("PersonnelNotificationRecipient")->insert($this->personnelNotification_consultationRequest->toArrayForDbEntry());
        $this->connection->table("PersonnelNotificationRecipient")->insert($this->personnelNotificationOne_consultationSession->toArrayForDbEntry());
        $this->connection->table("PersonnelNotificationRecipient")->insert($this->personnelNotificationTwo_comment->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Notification")->truncate();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ConsultationSetup")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("ConsultationRequest")->truncate();
        $this->connection->table("ConsultationRequestNotification")->truncate();
        $this->connection->table("ConsultationSession")->truncate();
        $this->connection->table("ConsultationSessionNotification")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("WorksheetForm")->truncate();
        $this->connection->table("Mission")->truncate();
        $this->connection->table("Worksheet")->truncate();
        $this->connection->table("Comment")->truncate();
        $this->connection->table("CommentNotification")->truncate();
        $this->connection->table("PersonnelNotificationRecipient")->truncate();
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 3,
            "list" => [
                [
                    "id" => $this->personnelNotification_consultationRequest->id,
                    "message" => $this->personnelNotification_consultationRequest->notification->message,
                    "notifiedTime" => $this->personnelNotification_consultationRequest->notifiedTime,
                    "read" => $this->personnelNotification_consultationRequest->readStatus,
                    "consultationRequest" => [
                        "id" => $this->consultationRequest->id,
                        "programConsultation" => [
                            "id" => $this->consultationRequest->consultant->id,
                        ],
                    ],
                    "consultationSession" => null,
                    "comment" => null,
                ],
                [
                    "id" => $this->personnelNotificationOne_consultationSession->id,
                    "message" => $this->personnelNotificationOne_consultationSession->notification->message,
                    "notifiedTime" => $this->personnelNotificationOne_consultationSession->notifiedTime,
                    "read" => $this->personnelNotificationOne_consultationSession->readStatus,
                    "consultationRequest" => null,
                    "consultationSession" => [
                        "id" => $this->consultationSession->id,
                        "programConsultation" => [
                            "id" => $this->consultationSession->consultant->id,
                        ],
                    ],
                    "comment" => null,
                ],
                [
                    "id" => $this->personnelNotificationTwo_comment->id,
                    "message" => $this->personnelNotificationTwo_comment->notification->message,
                    "notifiedTime" => $this->personnelNotificationTwo_comment->notifiedTime,
                    "read" => $this->personnelNotificationTwo_comment->readStatus,
                    "consultationRequest" => null,
                    "consultationSession" => null,
                    "comment" => [
                        "id" => $this->comment->id,
                        "worksheet" => [
                            "id" => $this->comment->worksheet->id,
                            "participant" => [
                                "id" => $this->comment->worksheet->participant->id,
                                "program" => [
                                    "id" => $this->comment->worksheet->participant->program->id,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->get($this->notificationUri, $this->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_readStatusFilterSet()
    {
        $totalResponse = [
            "total" => 2,
        ];
        $objectResponse = [
            "id" => $this->personnelNotification_consultationRequest->id,
        ];
        $objectOneResponse = [
            "id" => $this->personnelNotificationOne_consultationSession->id,
        ];
        
        $uri = $this->notificationUri . "?readStatus=false";
        $this->get($uri, $this->personnel->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($objectResponse)
                ->seeJsonContains($objectOneResponse)
                ->seeStatusCode(200);
    }

}
