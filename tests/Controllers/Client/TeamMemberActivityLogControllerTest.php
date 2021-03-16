<?php

namespace Tests\Controllers\Client;

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
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\RecordOfWorksheetForm;
use Tests\Controllers\RecordPreparation\Firm\Team\Member\RecordOfTeamMemberActivityLog;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;
use Tests\Controllers\RecordPreparation\Shared\RecordOfActivityLog;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class TeamMemberActivityLogControllerTest extends ClientTestCase
{
    protected $teamMemberActivityLogUri;
    protected $teamMemberActivityLogOne;
    protected $teamMemberActivityLogTwo;
    protected $consultationRequestActivityLogTwo;
    protected $consultationRequestActivityLog;
    protected $consultationSessionActivityLog;
    protected $worksheetActivityLog;
    protected $commentActivityLog;
    protected $viewLearningMaterialActivityLog;

    protected function setUp(): void
    {
        parent::setUp();
        $firm = $this->client->firm;
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('ActivityLog')->truncate();
        $this->connection->table('TeamMemberActivityLog')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('ConsultationRequest')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
        $this->connection->table('Worksheet')->truncate();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('LearningMaterial')->truncate();
        $this->connection->table('ConsultationRequestActivityLog')->truncate();
        $this->connection->table('ConsultationSessionActivityLog')->truncate();
        $this->connection->table('WorksheetActivityLog')->truncate();
        $this->connection->table('CommentActivityLog')->truncate();
        $this->connection->table('ViewLearningMaterialActivityLog')->truncate();
        
        $team = new RecordOfTeam($firm, $this->client, '99');
        $this->connection->table('Team')->insert($team->toArrayForDbEntry());
        
        $member = new RecordOfMember($team, $this->client, '99');
        $this->connection->table('T_Member')->insert($member->toArrayForDbEntry());

        $this->teamMemberActivityLogUri = $this->clientUri . "/{$member->id}/team-member-activity-logs";
        
        $activityLogOne = new RecordOfActivityLog('1');
        $activityLogTwo = new RecordOfActivityLog('2');
        $this->connection->table('ActivityLog')->insert($activityLogOne->toArrayForDbEntry());
        $this->connection->table('ActivityLog')->insert($activityLogTwo->toArrayForDbEntry());
        
        $this->teamMemberActivityLogOne = new RecordOfTeamMemberActivityLog($member, $activityLogOne);
        $this->teamMemberActivityLogTwo = new RecordOfTeamMemberActivityLog($member, $activityLogTwo);
        $this->connection->table('TeamMemberActivityLog')->insert($this->teamMemberActivityLogOne->toArrayForDbEntry());
        $this->connection->table('TeamMemberActivityLog')->insert($this->teamMemberActivityLogTwo->toArrayForDbEntry());

        $program = new RecordOfProgram($firm, '99');
        $this->connection->table('Program')->insert($program->toArrayForDbEntry());
        
        $consultationSetup = new RecordOfConsultationSetup($program, null, null, '99');
        $this->connection->table('ConsultationSetup')->insert($consultationSetup->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($program, '99');
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());
        
        $personnel = new RecordOfPersonnel($firm, '99');
        $this->connection->table('Personnel')->insert($personnel->toArrayForDbEntry());
        
        $consultant = new RecordOfConsultant($program, $personnel, '99');
        $this->connection->table('Consultant')->insert($consultant->toArrayForDbEntry());
        
        $form = new RecordOfForm('99');
        $this->connection->table('Form')->insert($form->toArrayForDbEntry());
        
        $formRecord = new RecordOfFormRecord($form, '99');
        $this->connection->table('FormRecord')->insert($formRecord->toArrayForDbEntry());
        
        $worksheetForm = new RecordOfWorksheetForm($firm, $form);
        $this->connection->table('WorksheetForm')->insert($worksheetForm->toArrayForDbEntry());

        $mission = new RecordOfMission($program, $worksheetForm, '99', null);
        $this->connection->table('Mission')->insert($mission->toArrayForDbEntry());
        
        $consultationRequest = new RecordOfConsultationRequest($consultationSetup, $participant, $consultant, '99');
        $this->connection->table('ConsultationRequest')->insert($consultationRequest->toArrayForDbEntry());

        $consultationSession = new RecordOfConsultationSession($consultationSetup, $participant, $consultant, '99');
        $this->connection->table('ConsultationSession')->insert($consultationSession->toArrayForDbEntry());

        $worksheet = new RecordOfWorksheet($participant, $formRecord, $mission, '99');
        $this->connection->table('Worksheet')->insert($worksheet->toArrayForDbEntry());

        $comment = new RecordOfComment($worksheet, '99');
        $this->connection->table('Comment')->insert($comment->toArrayForDbEntry());
 
        $learningMaterial = new RecordOfLearningMaterial($mission, '99');
        $this->connection->table('LearningMaterial')->insert($learningMaterial->toArrayForDbEntry());
        
        $this->consultationRequestActivityLogTwo = new RecordOfConsultationRequestActivityLog($consultationRequest, $activityLogTwo);
        $this->connection->table('ConsultationRequestActivityLog')->insert($this->consultationRequestActivityLogTwo->toArrayForDbEntry());
        
        $this->consultationRequestActivityLog = new RecordOfConsultationRequestActivityLog($consultationRequest, $activityLogOne);
        $this->consultationSessionActivityLog = new RecordOfConsultationSessionActivityLog($consultationSession, $activityLogOne);
        $this->worksheetActivityLog = new RecordOfWorksheetActivityLog($worksheet, $activityLogOne);
        $this->commentActivityLog = new RecordOfCommentActivityLog($comment, $activityLogOne);
        $this->viewLearningMaterialActivityLog = new RecordOfViewLearningMaterialActivityLog($participant, $learningMaterial, $activityLogOne);
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('ActivityLog')->truncate();
        $this->connection->table('TeamMemberActivityLog')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('ConsultationRequest')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
        $this->connection->table('Worksheet')->truncate();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('LearningMaterial')->truncate();
        $this->connection->table('ConsultationRequestActivityLog')->truncate();
        $this->connection->table('ConsultationSessionActivityLog')->truncate();
        $this->connection->table('WorksheetActivityLog')->truncate();
        $this->connection->table('CommentActivityLog')->truncate();
        $this->connection->table('ViewLearningMaterialActivityLog')->truncate();
    }
    
    public function test_show_200()
    {
        $this->connection->table('ConsultationRequestActivityLog')->insert($this->consultationRequestActivityLog->toArrayForDbEntry());
        $uri = $this->teamMemberActivityLogUri . "/{$this->teamMemberActivityLogOne->id}";
        $this->get($uri, $this->client->token)
                ->seeStatusCode(200);
        
        $response = [
            'id' => $this->teamMemberActivityLogOne->id,
            'message' => $this->teamMemberActivityLogOne->activityLog->message,
            'occuredTime' => $this->teamMemberActivityLogOne->activityLog->occuredTime,
            'consultationRequest' => [
                'id' => $this->consultationRequestActivityLog->consultationRequest->id,
                'startTime' => $this->consultationRequestActivityLog->consultationRequest->startDateTime,
                'endTime' => $this->consultationRequestActivityLog->consultationRequest->endDateTime,
                'consultant' => [
                    'id' => $this->consultationRequestActivityLog->consultationRequest->consultant->id,
                    'personnel' => [
                        'id' => $this->consultationRequestActivityLog->consultationRequest->consultant->personnel->id,
                        'name' => $this->consultationRequestActivityLog->consultationRequest->consultant->personnel->getFullName(),
                    ],
                ],
            ],
            'consultationSession' => null,
            'worksheet' => null,
            'comment' => null,
            'learningMaterial' => null,
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_consultationSession_200()
    {
        $this->connection->table('ConsultationSessionActivityLog')->insert($this->consultationSessionActivityLog->toArrayForDbEntry());
        $uri = $this->teamMemberActivityLogUri . "/{$this->teamMemberActivityLogOne->id}";
        $this->get($uri, $this->client->token)
                ->seeStatusCode(200);
        
        $response = [
            'id' => $this->teamMemberActivityLogOne->id,
            'message' => $this->teamMemberActivityLogOne->activityLog->message,
            'occuredTime' => $this->teamMemberActivityLogOne->activityLog->occuredTime,
            'consultationRequest' => null,
            'consultationSession' => [
                'id' => $this->consultationSessionActivityLog->consultationSession->id,
                'startTime' => $this->consultationSessionActivityLog->consultationSession->startDateTime,
                'endTime' => $this->consultationSessionActivityLog->consultationSession->endDateTime,
                'consultant' => [
                    'id' => $this->consultationSessionActivityLog->consultationSession->consultant->id,
                    'personnel' => [
                        'id' => $this->consultationSessionActivityLog->consultationSession->consultant->personnel->id,
                        'name' => $this->consultationSessionActivityLog->consultationSession->consultant->personnel->getFullName(),
                    ],
                ],
            ],
            'worksheet' => null,
            'comment' => null,
            'learningMaterial' => null,
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_worksheet_200()
    {
        $this->connection->table('WorksheetActivityLog')->insert($this->worksheetActivityLog->toArrayForDbEntry());
        $uri = $this->teamMemberActivityLogUri . "/{$this->teamMemberActivityLogOne->id}";
        $this->get($uri, $this->client->token)
                ->seeStatusCode(200);
        
        $response = [
            'id' => $this->teamMemberActivityLogOne->id,
            'message' => $this->teamMemberActivityLogOne->activityLog->message,
            'occuredTime' => $this->teamMemberActivityLogOne->activityLog->occuredTime,
            'consultationRequest' => null,
            'consultationSession' => null,
            'worksheet' => [
                'id' => $this->worksheetActivityLog->worksheet->id,
                'name' => $this->worksheetActivityLog->worksheet->name,
                'mission' => [
                    'id' => $this->worksheetActivityLog->worksheet->mission->id,
                    'name' => $this->worksheetActivityLog->worksheet->mission->name,
                    'position' => $this->worksheetActivityLog->worksheet->mission->position,
                ],
            ],
            'comment' => null,
            'learningMaterial' => null,
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_comment_200()
    {
        $this->connection->table('CommentActivityLog')->insert($this->commentActivityLog->toArrayForDbEntry());
        $uri = $this->teamMemberActivityLogUri . "/{$this->teamMemberActivityLogOne->id}";
        $this->get($uri, $this->client->token)
                ->seeStatusCode(200);
        
        $response = [
            'id' => $this->teamMemberActivityLogOne->id,
            'message' => $this->teamMemberActivityLogOne->activityLog->message,
            'occuredTime' => $this->teamMemberActivityLogOne->activityLog->occuredTime,
            'consultationRequest' => null,
            'consultationSession' => null,
            'worksheet' => null,
            'comment' => [
                'id' => $this->commentActivityLog->comment->id,
                'worksheet' => [
                    'id' => $this->commentActivityLog->comment->worksheet->id,
                    'name' => $this->commentActivityLog->comment->worksheet->name,
                ],
            ],
            'learningMaterial' => null,
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_viewLearningMaterial_200()
    {
        $this->connection->table('ViewLearningMaterialActivityLog')->insert($this->viewLearningMaterialActivityLog->toArrayForDbEntry());
        $uri = $this->teamMemberActivityLogUri . "/{$this->teamMemberActivityLogOne->id}";
        $this->get($uri, $this->client->token)
                ->seeStatusCode(200);
        
        $response = [
            'id' => $this->teamMemberActivityLogOne->id,
            'message' => $this->teamMemberActivityLogOne->activityLog->message,
            'occuredTime' => $this->teamMemberActivityLogOne->activityLog->occuredTime,
            'consultationRequest' => null,
            'consultationSession' => null,
            'worksheet' => null,
            'comment' => null,
            'learningMaterial' => [
                'id' => $this->viewLearningMaterialActivityLog->learningMaterial->id,
                'name' => $this->viewLearningMaterialActivityLog->learningMaterial->name,
                'mission' => [
                    'id' => $this->viewLearningMaterialActivityLog->learningMaterial->mission->id,
                    'name' => $this->viewLearningMaterialActivityLog->learningMaterial->mission->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    public function test_showAll_200()
    {
        $this->connection->table('ConsultationSessionActivityLog')->insert($this->consultationSessionActivityLog->toArrayForDbEntry());
        $this->get($this->teamMemberActivityLogUri, $this->client->token)
                ->seeStatusCode(200);
        
        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);
        
        $consultationSessionResponse = [
            'id' => $this->teamMemberActivityLogOne->id,
            'message' => $this->teamMemberActivityLogOne->activityLog->message,
            'occuredTime' => $this->teamMemberActivityLogOne->activityLog->occuredTime,
            'consultationRequest' => null,
            'consultationSession' => [
                'id' => $this->consultationSessionActivityLog->consultationSession->id,
                'startTime' => $this->consultationSessionActivityLog->consultationSession->startDateTime,
                'endTime' => $this->consultationSessionActivityLog->consultationSession->endDateTime,
                'consultant' => [
                    'id' => $this->consultationSessionActivityLog->consultationSession->consultant->id,
                    'personnel' => [
                        'id' => $this->consultationSessionActivityLog->consultationSession->consultant->personnel->id,
                        'name' => $this->consultationSessionActivityLog->consultationSession->consultant->personnel->getFullName(),
                    ],
                ],
            ],
            'worksheet' => null,
            'comment' => null,
            'learningMaterial' => null,
        ];
        $this->seeJsonContains($consultationSessionResponse);
        $consultationRequestResponse = [
            'id' => $this->teamMemberActivityLogTwo->id,
            'message' => $this->teamMemberActivityLogTwo->activityLog->message,
            'occuredTime' => $this->teamMemberActivityLogTwo->activityLog->occuredTime,
            'consultationRequest' => [
                'id' => $this->consultationRequestActivityLogTwo->consultationRequest->id,
                'startTime' => $this->consultationRequestActivityLogTwo->consultationRequest->startDateTime,
                'endTime' => $this->consultationRequestActivityLogTwo->consultationRequest->endDateTime,
                'consultant' => [
                    'id' => $this->consultationRequestActivityLogTwo->consultationRequest->consultant->id,
                    'personnel' => [
                        'id' => $this->consultationRequestActivityLogTwo->consultationRequest->consultant->personnel->id,
                        'name' => $this->consultationRequestActivityLogTwo->consultationRequest->consultant->personnel->getFullName(),
                    ],
                ],
            ],
            'consultationSession' => null,
            'worksheet' => null,
            'comment' => null,
            'learningMaterial' => null,
        ];
        $this->seeJsonContains($consultationRequestResponse);
    }
}
