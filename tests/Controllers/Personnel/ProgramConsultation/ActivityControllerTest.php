<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use DateTime;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\RecordOfInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfParticipantInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivity;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivityType;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;

class ActivityControllerTest extends ExtendedConsultantTestCase
{

    protected $allValidParticipantInvitationUri;
    protected $participantInviteeOne;
    protected $participantInviteeTwo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('ParticipantInvitee')->truncate();
        
        $program = $this->consultant->program;
        
        $participantOne = new RecordOfParticipant($program, 1);
        
        $activityTypeOne = new RecordOfActivityType($program, 1);
        
        $activityOne = new RecordOfActivity($activityTypeOne, 1);
        $activityTwo = new RecordOfActivity($activityTypeOne, 2);
        
        $activityParticipantOne = new RecordOfActivityParticipant($activityTypeOne, null, 1);
        
        $inviteeOne = new RecordOfInvitee($activityOne, $activityParticipantOne, 1);
        $inviteeTwo = new RecordOfInvitee($activityTwo, $activityParticipantOne, 2);
        
        $this->participantInviteeOne = new RecordOfParticipantInvitee($participantOne, $inviteeOne);
        $this->participantInviteeTwo = new RecordOfParticipantInvitee($participantOne, $inviteeTwo);
        
        $this->allValidParticipantInvitationUri = $this->consultantUri . "/program-participants/{$participantOne->id}/valid-activity-invitations";
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('ParticipantInvitee')->truncate();
    }
    
    protected function viewAllValidInvitationToParticipant()
    {
        $this->persistConsultantDependency();
        
        $this->participantInviteeOne->participant->insert($this->connection);
        
        $this->participantInviteeOne->invitee->activity->activityType->insert($this->connection);
        
        $this->participantInviteeOne->invitee->activity->insert($this->connection);
        $this->participantInviteeTwo->invitee->activity->insert($this->connection);
        
        $this->participantInviteeOne->invitee->activityParticipant->insert($this->connection);
        
        $this->participantInviteeOne->insert($this->connection);
        $this->participantInviteeTwo->insert($this->connection);
        
        $this->get($this->allValidParticipantInvitationUri, $this->consultant->personnel->token);
    }
    public function test_viewAllValidInvitationToParticipant_200()
    {
$this->disableExceptionHandling();
        $this->viewAllValidInvitationToParticipant();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 2,
            'list' => [
                [
                    'id' => $this->participantInviteeOne->invitee->id,
                    'anInitiator' => $this->participantInviteeOne->invitee->anInitiator,
                    'activity' => [
                        'id' => $this->participantInviteeOne->invitee->activity->id,
                        'name' => $this->participantInviteeOne->invitee->activity->name,
                        'description' => $this->participantInviteeOne->invitee->activity->description,
                        'startTime' => $this->participantInviteeOne->invitee->activity->startDateTime,
                        'endTime' => $this->participantInviteeOne->invitee->activity->endDateTime,
                        'location' => $this->participantInviteeOne->invitee->activity->location,
                        'note' => $this->participantInviteeOne->invitee->activity->note,
                        'cancelled' => $this->participantInviteeOne->invitee->activity->cancelled,
                    ],
                ],
                [
                    'id' => $this->participantInviteeTwo->invitee->id,
                    'anInitiator' => $this->participantInviteeTwo->invitee->anInitiator,
                    'activity' => [
                        'id' => $this->participantInviteeTwo->invitee->activity->id,
                        'name' => $this->participantInviteeTwo->invitee->activity->name,
                        'description' => $this->participantInviteeTwo->invitee->activity->description,
                        'startTime' => $this->participantInviteeTwo->invitee->activity->startDateTime,
                        'endTime' => $this->participantInviteeTwo->invitee->activity->endDateTime,
                        'location' => $this->participantInviteeTwo->invitee->activity->location,
                        'note' => $this->participantInviteeTwo->invitee->activity->note,
                        'cancelled' => $this->participantInviteeTwo->invitee->activity->cancelled,
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewAllValidInvitationToParticipant_excludeCancelledInvitation()
    {
        $this->participantInviteeOne->invitee->cancelled = true;
        
        $this->viewAllValidInvitationToParticipant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonDoesntContains(['id' => $this->participantInviteeOne->invitee->id]);
        $this->seeJsonContains(['id' => $this->participantInviteeTwo->invitee->id]);
    }
    public function test_viewAllValidInvitationToParticipant_excludeInvitationToOtherParticipant()
    {
        $otherParticipant= new RecordOfParticipant($this->consultant->program, 'other');
        $otherParticipant->insert($this->connection);
        $this->participantInviteeTwo->participant = $otherParticipant;
        
        $this->viewAllValidInvitationToParticipant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonContains(['id' => $this->participantInviteeOne->invitee->id]);
        $this->seeJsonDoesntContains(['id' => $this->participantInviteeTwo->invitee->id]);
    }
    public function test_viewAllValidInvitationToParticipant_unmanagedParticipant_belongsInOtherProgram_returnEmptySet()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->participantInviteeOne->participant->program = $otherProgram;
        
        $this->viewAllValidInvitationToParticipant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 0]);
        $this->seeJsonDoesntContains(['id' => $this->participantInviteeOne->invitee->id]);
        $this->seeJsonDoesntContains(['id' => $this->participantInviteeTwo->invitee->id]);
    }
    public function test_viewAllValidInvitationToParticipant_fromFilter()
    {
        $this->participantInviteeOne->invitee->activity->startDateTime = (new DateTime('-25 hours'))->format('Y-m-d H:i:s');
        $this->participantInviteeOne->invitee->activity->endDateTime = (new DateTime('-24 hours'))->format('Y-m-d H:i:s');
        
        $time = (new DateTime())->format('Y-m-d H:i:s');
        $this->allValidParticipantInvitationUri .= "?from=$time";
        
        $this->viewAllValidInvitationToParticipant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonDoesntContains(['id' => $this->participantInviteeOne->invitee->id]);
        $this->seeJsonContains(['id' => $this->participantInviteeTwo->invitee->id]);
    }
    public function test_viewAllValidInvitationToParticipant_toFilter()
    {
        $this->participantInviteeOne->invitee->activity->startDateTime = (new DateTime('-25 hours'))->format('Y-m-d H:i:s');
        $this->participantInviteeOne->invitee->activity->endDateTime = (new DateTime('-24 hours'))->format('Y-m-d H:i:s');
        
        $time = (new DateTime())->format('Y-m-d H:i:s');
        $this->allValidParticipantInvitationUri .= "?to=$time";
        
        $this->viewAllValidInvitationToParticipant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonContains(['id' => $this->participantInviteeOne->invitee->id]);
        $this->seeJsonDoesntContains(['id' => $this->participantInviteeTwo->invitee->id]);
    }
    public function test_viewAllValidInvitationToParticipant_setOrder()
    {
        $this->participantInviteeOne->invitee->activity->startDateTime = (new DateTime('-25 hours'))->format('Y-m-d H:i:s');
        $this->participantInviteeOne->invitee->activity->endDateTime = (new DateTime('-24 hours'))->format('Y-m-d H:i:s');
        
        $this->allValidParticipantInvitationUri .= "?order=ASC&pageSize=1";
        
        $this->viewAllValidInvitationToParticipant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 2]);
        $this->seeJsonContains(['id' => $this->participantInviteeOne->invitee->id]);
        $this->seeJsonDoesntContains(['id' => $this->participantInviteeTwo->invitee->id]);
    }
    public function test_viewAllValidInvitationToParticipant_inactiveConsultant_403()
    {
        $this->consultant->active = false;
        
        $this->viewAllValidInvitationToParticipant();
        $this->seeStatusCode(403);
    }
    
    protected function viewParticipantInvitationDetail()
    {
        $this->persistConsultantDependency();
        
        $this->participantInviteeOne->participant->insert($this->connection);
        $this->participantInviteeOne->invitee->activity->activityType->insert($this->connection);
        $this->participantInviteeOne->invitee->activity->insert($this->connection);
        $this->participantInviteeOne->invitee->activityParticipant->insert($this->connection);
        $this->participantInviteeOne->insert($this->connection);
        
        $uri = $this->consultantUri . "/participant-activity-invitations/{$this->participantInviteeOne->invitee->id}";
        $this->get($uri, $this->consultant->personnel->token);
    }
    public function test_viewParticipantInvitationDetail_200()
    {
$this->disableExceptionHandling();
        $this->viewParticipantInvitationDetail();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->participantInviteeOne->invitee->id,
            'anInitiator' => $this->participantInviteeOne->invitee->anInitiator,
            'activity' => [
                'id' => $this->participantInviteeOne->invitee->activity->id,
                'name' => $this->participantInviteeOne->invitee->activity->name,
                'description' => $this->participantInviteeOne->invitee->activity->description,
                'startTime' => $this->participantInviteeOne->invitee->activity->startDateTime,
                'endTime' => $this->participantInviteeOne->invitee->activity->endDateTime,
                'location' => $this->participantInviteeOne->invitee->activity->location,
                'note' => $this->participantInviteeOne->invitee->activity->note,
                'cancelled' => $this->participantInviteeOne->invitee->activity->cancelled,
            ],
            'activityParticipant' => [
                'id' => $this->participantInviteeOne->invitee->activityParticipant->id,
                'reportForm' => null,
            ],
            'report' => null,
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewParticipantInvitationDetail_inactiveConsultant_403()
    {
        $this->consultant->active = false;
        
        $this->viewParticipantInvitationDetail();
        $this->seeStatusCode(403);
    }
    public function test_viewParticipantInvitationDetail_unmanagedInvitation_participantBelongsToOtherProgram_404()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->participantInviteeOne->participant->program = $otherProgram;
        
        $this->viewParticipantInvitationDetail();
        $this->seeStatusCode(404);
    }

}
