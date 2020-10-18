<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Consultant\RecordOfConsultantActivityLog,
    Firm\Program\Participant\ConsultationRequest\RecordOfConsultationRequestActivityLog,
    Firm\Program\Participant\ConsultationSession\RecordOfConsultationSessionActivityLog,
    Firm\Program\Participant\RecordOfConsultationRequest,
    Firm\Program\Participant\RecordOfConsultationSession,
    Firm\Program\Participant\RecordOfWorksheet,
    Firm\Program\Participant\Worksheet\Comment\RecordOfCommentActivityLog,
    Firm\Program\Participant\Worksheet\RecordOfComment,
    Firm\Program\RecordOfConsultationSetup,
    Firm\Program\RecordOfMission,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfFeedbackForm,
    Firm\RecordOfWorksheetForm,
    Shared\RecordOfActivityLog,
    Shared\RecordOfForm,
    Shared\RecordOfFormRecord
};

class ActivityLogControllerTest extends ProgramConsultationTestCase
{
    protected $activityLogUri;
    protected $consultantActivityLog_consultationRequest;
    protected $consultantActivityLogOne_consultationSession;
    protected $consultantActivityLogTwo_comment;
    
    protected $consultationRequest;
    protected $consultationSession;
    protected $comment;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->activityLogUri = $this->programConsultationUri . "/activity-logs";
        
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ConsultationSetup")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ConsultationRequest")->truncate();
        $this->connection->table("ConsultationSession")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("WorksheetForm")->truncate();
        $this->connection->table("Mission")->truncate();
        $this->connection->table("Worksheet")->truncate();
        $this->connection->table("Comment")->truncate();
        
        $this->connection->table("ActivityLog")->truncate();
        $this->connection->table("ConsultationRequestActivityLog")->truncate();
        $this->connection->table("ConsultationSessionActivityLog")->truncate();
        $this->connection->table("CommentActivityLog")->truncate();
        $this->connection->table("ConsultantActivityLog")->truncate();
        
        $program = $this->programConsultation->program;
        $firm = $program->firm;
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        

        $feedbackForm = new RecordOfFeedbackForm($firm, $form);
        $this->connection->table("FeedbackForm")->insert($feedbackForm->toArrayForDbEntry());
        
        $consultationSetup = new RecordOfConsultationSetup($program, $feedbackForm, $feedbackForm, 0);
        $this->connection->table("ConsultationSetup")->insert($consultationSetup->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($program, 0);
        $this->connection->table("Participant")->insert($participant->toArrayForDbEntry());
        
        $this->consultationRequest = new RecordOfConsultationRequest($consultationSetup, $participant, $this->programConsultation, 0);
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest->toArrayForDbEntry());
        
        $this->consultationSession = new RecordOfConsultationSession($consultationSetup, $participant, $this->programConsultation, 0);
        $this->connection->table("ConsultationSession")->insert($this->consultationSession->toArrayForDbEntry());
        
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
        
        $activityLog = new RecordOfActivityLog(0);
        $activityLogOne = new RecordOfActivityLog(1);
        $activityLogTwo = new RecordOfActivityLog(2);
        $this->connection->table("ActivityLog")->insert($activityLog->toArrayForDbEntry());
        $this->connection->table("ActivityLog")->insert($activityLogOne->toArrayForDbEntry());
        $this->connection->table("ActivityLog")->insert($activityLogTwo->toArrayForDbEntry());
        
        $consultationRequestActivityLog = new RecordOfConsultationRequestActivityLog($this->consultationRequest, $activityLog);
        $this->connection->table("ConsultationRequestActivityLog")->insert($consultationRequestActivityLog->toArrayForDbEntry());
        
        $consultationSessionActivityLog = new RecordOfConsultationSessionActivityLog($this->consultationSession, $activityLogOne);
        $this->connection->table("ConsultationSessionActivityLog")->insert($consultationSessionActivityLog->toArrayForDbEntry());
        
        $commentActivityLog = new RecordOfCommentActivityLog($this->comment, $activityLogTwo);
        $this->connection->table("CommentActivityLog")->insert($commentActivityLog->toArrayForDbEntry());
        
        $this->consultantActivityLog_consultationRequest = new RecordOfConsultantActivityLog($this->programConsultation, $activityLog);
        $this->consultantActivityLogOne_consultationSession = new RecordOfConsultantActivityLog($this->programConsultation, $activityLogOne);
        $this->consultantActivityLogTwo_comment = new RecordOfConsultantActivityLog($this->programConsultation, $activityLogTwo);
        $this->connection->table("ConsultantActivityLog")->insert($this->consultantActivityLog_consultationRequest->toArrayForDbEntry());
        $this->connection->table("ConsultantActivityLog")->insert($this->consultantActivityLogOne_consultationSession->toArrayForDbEntry());
        $this->connection->table("ConsultantActivityLog")->insert($this->consultantActivityLogTwo_comment->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("ActivityLog")->truncate();
        $this->connection->table("ConsultationRequestActivityLog")->truncate();
        $this->connection->table("ConsultationSessionActivityLog")->truncate();
        $this->connection->table("CommentActivityLog")->truncate();
        $this->connection->table("ConsultantActivityLog")->truncate();
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 3,
            "list" =>  [
                [
                    "id" => $this->consultantActivityLog_consultationRequest->id,
                    "message" => $this->consultantActivityLog_consultationRequest->activityLog->message,
                    "occuredTime" => $this->consultantActivityLog_consultationRequest->activityLog->occuredTime,
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
                    "id" => $this->consultantActivityLogOne_consultationSession->id,
                    "message" => $this->consultantActivityLogOne_consultationSession->activityLog->message,
                    "occuredTime" => $this->consultantActivityLogOne_consultationSession->activityLog->occuredTime,
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
                    "id" => $this->consultantActivityLogTwo_comment->id,
                    "message" => $this->consultantActivityLogTwo_comment->activityLog->message,
                    "occuredTime" => $this->consultantActivityLogTwo_comment->activityLog->occuredTime,
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
        $this->get($this->activityLogUri, $this->programConsultation->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
}
