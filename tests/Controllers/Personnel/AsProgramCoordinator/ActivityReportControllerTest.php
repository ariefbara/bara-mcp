<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use Tests\Controllers\RecordPreparation\Firm\Program\Activity\Invitee\RecordOfInviteeReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\RecordOfInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\Coordinator\RecordOfActivityInvitation;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfActivityInvitation as RecordOfActivityInvitation2;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivity;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivityType;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class ActivityReportControllerTest extends AsProgramCoordinatorTestCase
{
    protected $activity;
    protected $activityReportOne;
    protected $activityReportTwo, $teamParticipant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("InviteeReport")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Team")->truncate();
        $this->connection->table("TeamParticipant")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();        

        $program = $this->coordinator->program;
        $firm = $program->firm;
        
        $activityType = new RecordOfActivityType($program, 0);
        $this->connection->table("ActivityType")->insert($activityType->toArrayForDbEntry());
        
        $this->activity = new RecordOfActivity($activityType, 0);
        $this->connection->table("Activity")->insert($this->activity->toArrayForDbEntry());
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $inviteeOne = new RecordOfInvitee($this->activity, $activityParticipant = null, 1);
        $inviteeTwo = new RecordOfInvitee($this->activity, $activityParticipant = null, 2);
        $this->connection->table("Invitee")->insert($inviteeOne->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($inviteeTwo->toArrayForDbEntry());
        
        $formRecordOne = new RecordOfFormRecord($form, 1);
        $formRecordTwo = new RecordOfFormRecord($form, 2);
        $this->connection->table("FormRecord")->insert($formRecordOne->toArrayForDbEntry());
        $this->connection->table("FormRecord")->insert($formRecordTwo->toArrayForDbEntry());
        
        $this->activityReportOne = new RecordOfInviteeReport($inviteeOne, $formRecordOne);
        $this->activityReportTwo = new RecordOfInviteeReport($inviteeTwo, $formRecordTwo);
        $this->connection->table("InviteeReport")->insert($this->activityReportOne->toArrayForDbEntry());
        $this->connection->table("InviteeReport")->insert($this->activityReportTwo->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($program, 0);
        $this->connection->table("Participant")->insert($participant->toArrayForDbEntry());
        
        $team = new RecordOfTeam($firm, $client = null, 0);
        $this->connection->table("Team")->insert($team->toArrayForDbEntry());
        
        $this->teamParticipant = new RecordOfTeamProgramParticipation($team, $participant);
        $this->connection->table("TeamParticipant")->insert($this->teamParticipant->toArrayForDbEntry());
        
        $coordinatorInvitee = new RecordOfActivityInvitation($this->coordinator, $inviteeOne);
        $this->connection->table("CoordinatorInvitee")->insert($coordinatorInvitee->toArrayForDbEntry());
        
        $participantInvitee = new RecordOfActivityInvitation2($participant, $inviteeTwo);
        $this->connection->table("ParticipantInvitee")->insert($participantInvitee->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("InviteeReport")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Team")->truncate();
        $this->connection->table("TeamParticipant")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->activityReportOne->id,
            "invitee" => [
                "id" => $this->activityReportOne->invitee->id,
                "anInitiator" => $this->activityReportOne->invitee->anInitiator,
                "manager" => null,
                "coordinator" => [
                    "id" => $this->coordinator->id,
                    "name" => $this->coordinator->personnel->getFullName(),
                ],
                "consultant" => null,
                "participant" => null,
            ],
            "submitTime" => $this->activityReportOne->formRecord->submitTime,
            "stringFieldRecords" => [],
            "integerFieldRecords" => [],
            "textAreaFieldRecords" => [],
            "attachmentFieldRecords" => [],
            "singleSelectFieldRecords" => [],
            "multiSelectFieldRecords" => [],
        ];
        
        $uri = $this->asProgramCoordinatorUri . "/activity-reports/{$this->activityReportOne->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->activityReportOne->id,
                    "invitee" => [
                        "id" => $this->activityReportOne->invitee->id,
                        "anInitiator" => $this->activityReportOne->invitee->anInitiator,
                        "manager" => null,
                        "coordinator" => [
                            "id" => $this->coordinator->id,
                            "name" => $this->coordinator->personnel->getFullName(),
                        ],
                        "consultant" => null,
                        "participant" => null,
                    ],
                    "submitTime" => $this->activityReportOne->formRecord->submitTime,
                ],
                [
                    "id" => $this->activityReportTwo->id,
                    "invitee" => [
                        "id" => $this->activityReportTwo->invitee->id,
                        "anInitiator" => $this->activityReportTwo->invitee->anInitiator,
                        "manager" => null,
                        "coordinator" => null,
                        "consultant" => null,
                        "participant" => [
                            "id" => $this->teamParticipant->participant->id,
                            "name" => $this->teamParticipant->team->name,
                        ],
                    ],
                    "submitTime" => $this->activityReportTwo->formRecord->submitTime,
                ],
            ],
        ];
        
        $uri = $this->asProgramCoordinatorUri . "/activities/{$this->activity->id}/activity-reports";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
