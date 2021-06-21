<?php

namespace Tests\Controllers\Client\AsTeamMember\ProgramParticipation;

use Tests\Controllers\Client\AsTeamMember\ProgramParticipationTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfConsultantActivityLog;
use Tests\Controllers\RecordPreparation\Firm\Program\Mission\RecordOfLearningMaterial;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\ConsultationRequest\RecordOfConsultationRequestActivityLog;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\ConsultationSession\RecordOfConsultationSessionActivityLog;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfConsultationRequest;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfConsultationSession;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfViewLearningMaterialActivityLog;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfWorksheet;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\Comment\RecordOfCommentActivityLog;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfComment;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfWorksheetActivityLog;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFeedbackForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfWorksheetForm;
use Tests\Controllers\RecordPreparation\Firm\Team\Member\RecordOfTeamMemberActivityLog;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;
use Tests\Controllers\RecordPreparation\Shared\RecordOfActivityLog;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class ActivityLogControllerTest extends ProgramParticipationTestCase
{
    protected $activityLogUri;
    protected $activityLog;
    protected $consultationRequest;
    protected $activityLogOne_consultationSession;
    protected $consultationSession;
    protected $activityLogTwo_worksheet_otherMember;
    protected $worksheet;
    protected $activityLogThree_comment_fromConsultant;
    protected $comment;
    protected $learningMaterial;
    protected $activityLogFour_learningMaterial;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activityLogUri = $this->programParticipationUri . "/{$this->programParticipation->id}/activity-logs";
        
        $this->connection->table("ActivityLog")->truncate();
        $this->connection->table("ConsultationRequestActivityLog")->truncate();
        $this->connection->table("ConsultationSessionActivityLog")->truncate();
        $this->connection->table("WorksheetActivityLog")->truncate();
        $this->connection->table("CommentActivityLog")->truncate();
        $this->connection->table("ViewLearningMaterialActivityLog")->truncate();
        $this->connection->table("TeamMemberActivityLog")->truncate();
        $this->connection->table("ConsultantActivityLog")->truncate();
        
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ConsultationSetup")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("ConsultationRequest")->truncate();
        $this->connection->table("ConsultationSession")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("WorksheetForm")->truncate();
        $this->connection->table("Mission")->truncate();
        $this->connection->table("Worksheet")->truncate();
        $this->connection->table("Comment")->truncate();
        $this->connection->table("LearningMaterial")->truncate();
        
        $participant = $this->programParticipation->participant;
        $program = $participant->program;
        $firm = $program->firm;
        $team = $this->programParticipation->team;
        
        $client = new RecordOfClient($firm, 22);
        $this->connection->table('Client')->insert($client->toArrayForDbEntry());
        $otherMember = new RecordOfMember($team, $client, 22);
        $this->connection->table('T_Member')->insert($otherMember->toArrayForDbEntry());
        
        $this->activityLog = new RecordOfActivityLog(0);
        $this->activityLogOne_consultationSession = new RecordOfActivityLog(1);
        $this->activityLogTwo_worksheet_otherMember = new RecordOfActivityLog(2);
        $this->activityLogThree_comment_fromConsultant = new RecordOfActivityLog(3);
        $this->activityLogFour_learningMaterial = new RecordOfActivityLog(4);
        $this->connection->table("ActivityLog")->insert($this->activityLog->toArrayForDbEntry());
        $this->connection->table("ActivityLog")->insert($this->activityLogOne_consultationSession->toArrayForDbEntry());
        $this->connection->table("ActivityLog")->insert($this->activityLogTwo_worksheet_otherMember->toArrayForDbEntry());
        $this->connection->table("ActivityLog")->insert($this->activityLogThree_comment_fromConsultant->toArrayForDbEntry());
        $this->connection->table("ActivityLog")->insert($this->activityLogFour_learningMaterial->toArrayForDbEntry());
        
        $teamMemberActivityLog = new RecordOfTeamMemberActivityLog($this->teamMember, $this->activityLog);
        $teamMemberActivityLogOne = new RecordOfTeamMemberActivityLog($this->teamMember, $this->activityLogOne_consultationSession);
        $teamMemberActivityLogTwo = new RecordOfTeamMemberActivityLog($otherMember, $this->activityLogTwo_worksheet_otherMember);
//        $teamMemberActivityLogThree = new RecordOfTeamMemberActivityLog($this->teamMember, $this->activityLogThree_comment_fromConsultant);
        $teamMemberActivityLogFour = new RecordOfTeamMemberActivityLog($this->teamMember, $this->activityLogFour_learningMaterial);
        $this->connection->table("TeamMemberActivityLog")->insert($teamMemberActivityLog->toArrayForDbEntry());
        $this->connection->table("TeamMemberActivityLog")->insert($teamMemberActivityLogOne->toArrayForDbEntry());
        $this->connection->table("TeamMemberActivityLog")->insert($teamMemberActivityLogTwo->toArrayForDbEntry());
//        $this->connection->table("TeamMemberActivityLog")->insert($teamMemberActivityLogThree->toArrayForDbEntry());
        $this->connection->table("TeamMemberActivityLog")->insert($teamMemberActivityLogFour->toArrayForDbEntry());
        
        
        $personnel = new RecordOfPersonnel($firm, 0, 'personnel@email.org', 'password213');
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $feedbackForm = new RecordOfFeedbackForm($firm, $form);
        $this->connection->table("FeedbackForm")->insert($feedbackForm->toArrayForDbEntry());
        
        $consultationSetup = new RecordOfConsultationSetup($program, $feedbackForm, $feedbackForm, 0);
        $this->connection->table("ConsultationSetup")->insert($consultationSetup->toArrayForDbEntry());
        
        
        $consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->connection->table("Consultant")->insert($consultant->toArrayForDbEntry());
        
        $consultantActivityLog = new RecordOfConsultantActivityLog($consultant, $this->activityLogThree_comment_fromConsultant);
        $this->connection->table("ConsultantActivityLog")->insert($consultantActivityLog->toArrayForDbEntry());
        
        $this->consultationRequest = new RecordOfConsultationRequest($consultationSetup, $participant, $consultant, 0);
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest->toArrayForDbEntry());
        
        $consultationRequestActivityLog = new RecordOfConsultationRequestActivityLog($this->consultationRequest, $this->activityLog);
        $this->connection->table("ConsultationRequestActivityLog")->insert($consultationRequestActivityLog->toArrayForDbEntry());
        
        $this->consultationSession = new RecordOfConsultationSession($consultationSetup, $participant, $consultant, 0);
        $this->connection->table("ConsultationSession")->insert($this->consultationSession->toArrayForDbEntry());
        
        $consultationSessionActivityLogOne = new RecordOfConsultationSessionActivityLog($this->consultationSession, $this->activityLogOne_consultationSession);
        $this->connection->table("ConsultationSessionActivityLog")->insert($consultationSessionActivityLogOne->toArrayForDbEntry());
        
        $formRecord = new RecordOfFormRecord($form, 0);
        $this->connection->table("FormRecord")->insert($formRecord->toArrayForDbEntry());
        
        $worksheetForm = new RecordOfWorksheetForm($firm, $form);
        $this->connection->table("WorksheetForm")->insert($worksheetForm->toArrayForDbEntry());
        
        $mission = new RecordOfMission($program, $worksheetForm, 0, null);
        $this->connection->table("Mission")->insert($mission->toArrayForDbEntry());
        
        $this->worksheet = new RecordOfWorksheet($participant, $formRecord, $mission, 0);
        $this->connection->table("Worksheet")->insert($this->worksheet->toArrayForDbEntry());
        
        $worksheetActivityLog = new RecordOfWorksheetActivityLog($this->worksheet, $this->activityLogTwo_worksheet_otherMember);
        $this->connection->table("WorksheetActivityLog")->insert($worksheetActivityLog->toArrayForDbEntry());
        
        $this->comment = new RecordOfComment($this->worksheet, 0);
        $this->connection->table("Comment")->insert($this->comment->toArrayForDbEntry());
        
        $commentActivityLog = new RecordOfCommentActivityLog($this->comment, $this->activityLogThree_comment_fromConsultant);
        $this->connection->table("CommentActivityLog")->insert($commentActivityLog->toArrayForDbEntry());
        
        $this->learningMaterial = new RecordOfLearningMaterial($mission, 0);
        $this->connection->table("LearningMaterial")->insert($this->learningMaterial->toArrayForDbEntry());
        
        $viewLearningMaterialActivityLog = new RecordOfViewLearningMaterialActivityLog(
                $participant, $this->learningMaterial, $this->activityLogFour_learningMaterial);
        $this->connection->table("ViewLearningMaterialActivityLog")->insert($viewLearningMaterialActivityLog->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("ActivityLog")->truncate();
        $this->connection->table("ConsultationRequestActivityLog")->truncate();
        $this->connection->table("ConsultationSessionActivityLog")->truncate();
        $this->connection->table("WorksheetActivityLog")->truncate();
        $this->connection->table("CommentActivityLog")->truncate();
        $this->connection->table("ViewLearningMaterialActivityLog")->truncate();
        $this->connection->table("TeamMemberActivityLog")->truncate();
        $this->connection->table("ConsultantActivityLog")->truncate();
        
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ConsultationSetup")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("ConsultationRequest")->truncate();
        $this->connection->table("ConsultationSession")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("WorksheetForm")->truncate();
        $this->connection->table("Mission")->truncate();
        $this->connection->table("Worksheet")->truncate();
        $this->connection->table("Comment")->truncate();
        $this->connection->table("LearningMaterial")->truncate();
    }
    
    public function test_showAll_200()
    {
        $totalResponse = [
            "total" => 5,
        ];
        $consultationRequestObject = [
            "id" => $this->activityLog->id,
            "message" => $this->activityLog->message,
            "occuredTime" => $this->activityLog->occuredTime,
            "teamMember" => [
                "id" => $this->teamMember->id,
                "client" => [
                    "id" => $this->teamMember->client->id,
                    "name" => $this->teamMember->client->getFullName(),
                ],
            ],
            "consultationRequest" => [
                "id" => $this->consultationRequest->id,
                "startTime" => $this->consultationRequest->startDateTime,
                "endTime" => $this->consultationRequest->endDateTime,
                "consultant" => [
                    "id" => $this->consultationRequest->consultant->id,
                    "personnel" => [
                        "id" => $this->consultationRequest->consultant->personnel->id,
                        "name" => $this->consultationRequest->consultant->personnel->getFullName(),
                    ],
                ],
            ],
            "consultationSession" => null,
            "worksheet" => null,
            "comment" => null,
            "learningMaterial" => null,
        ];
        $consultationSessionObject = [
            "id" => $this->activityLogOne_consultationSession->id,
            "message" => $this->activityLogOne_consultationSession->message,
            "occuredTime" => $this->activityLogOne_consultationSession->occuredTime,
            "teamMember" => [
                "id" => $this->teamMember->id,
                "client" => [
                    "id" => $this->teamMember->client->id,
                    "name" => $this->teamMember->client->getFullName(),
                ],
            ],
            "consultationRequest" => null,
            "consultationSession" => [
                "id" => $this->consultationSession->id,
                "startTime" => $this->consultationSession->startDateTime,
                "endTime" => $this->consultationSession->endDateTime,
                "consultant" => [
                    "id" => $this->consultationSession->consultant->id,
                    "personnel" => [
                        "id" => $this->consultationSession->consultant->personnel->id,
                        "name" => $this->consultationSession->consultant->personnel->getFullName(),
                    ],
                ],
            ],
            "worksheet" => null,
            "comment" => null,
            "learningMaterial" => null,
        ];
        $worksheetObject = [
            "id" => $this->activityLogTwo_worksheet_otherMember->id,
            "message" => $this->activityLogTwo_worksheet_otherMember->message,
            "occuredTime" => $this->activityLogTwo_worksheet_otherMember->occuredTime,
            "teamMember" => [
                "id" => $this->teamMember->id,
                "client" => [
                    "id" => $this->teamMember->client->id,
                    "name" => $this->teamMember->client->getFullName(),
                ],
            ],
            "consultationRequest" => null,
            "consultationSession" => null,
            "worksheet" => [
                "id" => $this->worksheet->id,
                "name" => $this->worksheet->name,
                "mission" => [
                    "id" => $this->worksheet->mission->id,
                    "name" => $this->worksheet->mission->name,
                    "position" => $this->worksheet->mission->position,
                ],
            ],
            "comment" => null,
            "learningMaterial" => null,
        ];
        $commentObject = [
            "id" => $this->activityLogThree_comment_fromConsultant->id,
            "message" => $this->activityLogThree_comment_fromConsultant->message,
            "occuredTime" => $this->activityLogThree_comment_fromConsultant->occuredTime,
            "teamMember" => [
                "id" => $this->teamMember->id,
                "client" => [
                    "id" => $this->teamMember->client->id,
                    "name" => $this->teamMember->client->getFullName(),
                ],
            ],
            "consultationRequest" => null,
            "consultationSession" => null,
            "worksheet" => null,
            "comment" => [
                "id" => $this->comment->id,
                "worksheet" => [
                    "id" => $this->comment->worksheet->id,
                    "name" => $this->comment->worksheet->name,
                ],
            ],
            "learningMaterial" => null,
        ];
        $viewLearningMaterialObject = [
            "id" => $this->activityLogFour_learningMaterial->id,
            "message" => $this->activityLogFour_learningMaterial->message,
            "occuredTime" => $this->activityLogFour_learningMaterial->occuredTime,
            "teamMember" => [
                "id" => $this->teamMember->id,
                "client" => [
                    "id" => $this->teamMember->client->id,
                    "name" => $this->teamMember->client->getFullName(),
                ],
            ],
            "consultationRequest" => null,
            "consultationSession" => null,
            "worksheet" => null,
            "comment" => null,
            "learningMaterial" => [
                "id" => $this->learningMaterial->id,
                "name" => $this->learningMaterial->name,
                "mission" => [
                    "id" => $this->learningMaterial->mission->id,
                    "name" => $this->learningMaterial->mission->name,
                ],
            ],
        ];
        $uri = $this->activityLogUri . '/all';
        $this->get($uri, $this->teamMember->client->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($consultationRequestObject)
                ->seeJsonContains($consultationSessionObject)
                ->seeJsonContains($worksheetObject)
                ->seeJsonContains($commentObject)
                ->seeJsonContains($viewLearningMaterialObject)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveTeamMember_403()
    {
        $this->setTeamMembershipInactive();
        $uri = $this->activityLogUri . '/all';
        $this->get($uri, $this->teamMember->client->token)
                ->seeStatusCode(403);
    }
    
    public function test_showSelf_200()
    {
        $uri = $this->activityLogUri . '/self';
        $this->get($uri, $this->teamMember->client->token)
                ->seeStatusCode(200);
        $totalRecord = ['total' => 3];
        $this->seeJsonContains($totalRecord);
    }
    
    public function test_showShared_200()
    {
        $uri = $this->activityLogUri . '/shared';
        $this->get($uri, $this->teamMember->client->token)
                ->seeStatusCode(200);
        $totalRecord = ['total' => 2];
        $this->seeJsonContains($totalRecord);
    }
}
 