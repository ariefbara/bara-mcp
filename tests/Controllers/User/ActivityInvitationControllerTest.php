<?php

namespace Tests\Controllers\User;

use DateTime;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\RecordOfInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfActivityInvitation;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivity;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivityType;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class ActivityInvitationControllerTest extends UserTestCase
{

    protected $activityInvitationUri;
    protected $activityInvitationOne;
    protected $activityInvitationTwo;
    protected $activityInvitationThree;
    protected $userParticipantOne;
    protected $userParticipantTwo;
    protected $userParticipantThree_inactive;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activityInvitationUri = $this->userUri . "/activity-invitations";
        
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ParticipantInvitee')->truncate();
        $this->connection->table('UserParticipant')->truncate();

        $firmOne = new RecordOfFirm('1');
        $firmTwo = new RecordOfFirm('2');
        $firmThree = new RecordOfFirm('3');
        $this->connection->table('Firm')->insert($firmOne->toArrayForDbEntry());
        $this->connection->table('Firm')->insert($firmTwo->toArrayForDbEntry());
        $this->connection->table('Firm')->insert($firmThree->toArrayForDbEntry());
        
        $programOne = new RecordOfProgram($firmOne, '1');
        $programTwo = new RecordOfProgram($firmTwo, '2');
        $programThree = new RecordOfProgram($firmThree, '3');
        $this->connection->table('Program')->insert($programOne->toArrayForDbEntry());
        $this->connection->table('Program')->insert($programTwo->toArrayForDbEntry());
        $this->connection->table('Program')->insert($programThree->toArrayForDbEntry());

        $activityTypeOne = new RecordOfActivityType($programOne, '1');
        $activityTypeTwo = new RecordOfActivityType($programTwo, '2');
        $activityTypeThree = new RecordOfActivityType($programThree, '3');
        $this->connection->table('ActivityType')->insert($activityTypeOne->toArrayForDbEntry());
        $this->connection->table('ActivityType')->insert($activityTypeTwo->toArrayForDbEntry());
        $this->connection->table('ActivityType')->insert($activityTypeThree->toArrayForDbEntry());

        $activityParticipantOne = new RecordOfActivityParticipant($activityTypeOne, null, '1');
        $activityParticipantTwo = new RecordOfActivityParticipant($activityTypeTwo, null, '2');
        $activityParticipantThree = new RecordOfActivityParticipant($activityTypeThree, null, '3');
        $this->connection->table('ActivityParticipant')->insert($activityParticipantOne->toArrayForDbEntry());
        $this->connection->table('ActivityParticipant')->insert($activityParticipantTwo->toArrayForDbEntry());
        $this->connection->table('ActivityParticipant')->insert($activityParticipantThree->toArrayForDbEntry());

        $activityOne = new RecordOfActivity($activityTypeOne, '1');
        $activityTwo = new RecordOfActivity($activityTypeTwo, '2');
        $activityTwo->startDateTime = (new DateTime('+1 months'))->format('Y-m-d H:i:s');
        $activityThree = new RecordOfActivity($activityTypeThree, '3');
        $activityThree->startDateTime = (new DateTime('+2 months'))->format('Y-m-d H:i:s');
        $this->connection->table('Activity')->insert($activityOne->toArrayForDbEntry());
        $this->connection->table('Activity')->insert($activityTwo->toArrayForDbEntry());
        $this->connection->table('Activity')->insert($activityThree->toArrayForDbEntry());

        $this->activityInvitationOne = new RecordOfInvitee($activityOne, $activityParticipantOne, '1');
        $this->activityInvitationTwo = new RecordOfInvitee($activityTwo, $activityParticipantTwo, '2');
        $this->activityInvitationTwo->cancelled = true;
        $this->activityInvitationTwo->willAttend = true;
        $this->activityInvitationThree = new RecordOfInvitee($activityThree, $activityParticipantThree, '3');
        $this->connection->table('Invitee')->insert($this->activityInvitationOne->toArrayForDbEntry());
        $this->connection->table('Invitee')->insert($this->activityInvitationTwo->toArrayForDbEntry());
        $this->connection->table('Invitee')->insert($this->activityInvitationThree->toArrayForDbEntry());

        $participantOne = new RecordOfParticipant($programOne, '1');
        $participantTwo = new RecordOfParticipant($programTwo, '2');
        $participantThree = new RecordOfParticipant($programThree, '3');
        $participantThree->active = false;
        $this->connection->table('Participant')->insert($participantOne->toArrayForDbEntry());
        $this->connection->table('Participant')->insert($participantTwo->toArrayForDbEntry());
        $this->connection->table('Participant')->insert($participantThree->toArrayForDbEntry());
        
        $participantInviteeOne = new RecordOfActivityInvitation($participantOne, $this->activityInvitationOne);
        $participantInviteeTwo = new RecordOfActivityInvitation($participantTwo, $this->activityInvitationTwo);
        $participantInviteeThree = new RecordOfActivityInvitation($participantThree, $this->activityInvitationThree);
        $this->connection->table('ParticipantInvitee')->insert($participantInviteeOne->toArrayForDbEntry());
        $this->connection->table('ParticipantInvitee')->insert($participantInviteeTwo->toArrayForDbEntry());
        $this->connection->table('ParticipantInvitee')->insert($participantInviteeThree->toArrayForDbEntry());

        $this->userParticipantOne = new RecordOfUserParticipant($this->user, $participantOne);
        $this->userParticipantTwo = new RecordOfUserParticipant($this->user, $participantTwo);
        $this->userParticipantThree_inactive = new RecordOfUserParticipant($this->user, $participantThree);
        $this->connection->table('UserParticipant')->insert($this->userParticipantOne->toArrayForDbEntry());
        $this->connection->table('UserParticipant')->insert($this->userParticipantTwo->toArrayForDbEntry());
        $this->connection->table('UserParticipant')->insert($this->userParticipantThree_inactive->toArrayForDbEntry());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ParticipantInvitee')->truncate();
        $this->connection->table('UserParticipant')->truncate();
    }
    
    public function test_showAll_200()
    {
        $this->get($this->activityInvitationUri, $this->user->token)
                ->seeStatusCode(200);
        
        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);
        
        $invitationOneResponse = [
            'id' => $this->activityInvitationOne->id,
            'anInitiator' => $this->activityInvitationOne->anInitiator,
            'willAttend' => $this->activityInvitationOne->willAttend,
            'cancelled' => $this->activityInvitationOne->cancelled,
            'reportSubmitted' => false,
            'activity' => [
                'id' => $this->activityInvitationOne->activity->id,
                'name' => $this->activityInvitationOne->activity->name,
                'description' => $this->activityInvitationOne->activity->description,
                'startTime' => $this->activityInvitationOne->activity->startDateTime,
                'endTime' => $this->activityInvitationOne->activity->endDateTime,
                'location' => $this->activityInvitationOne->activity->location,
                'note' => $this->activityInvitationOne->activity->note,
            ],
            'participant' => [
                'id' => $this->userParticipantOne->participant->id,
                'program' => [
                    'id' => $this->userParticipantOne->participant->program->id,
                    'name' => $this->userParticipantOne->participant->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($invitationOneResponse);
        $invitationTwoResponse = [
            'id' => $this->activityInvitationTwo->id,
            'anInitiator' => $this->activityInvitationTwo->anInitiator,
            'willAttend' => $this->activityInvitationTwo->willAttend,
            'cancelled' => $this->activityInvitationTwo->cancelled,
            'reportSubmitted' => false,
            'activity' => [
                'id' => $this->activityInvitationTwo->activity->id,
                'name' => $this->activityInvitationTwo->activity->name,
                'description' => $this->activityInvitationTwo->activity->description,
                'startTime' => $this->activityInvitationTwo->activity->startDateTime,
                'endTime' => $this->activityInvitationTwo->activity->endDateTime,
                'location' => $this->activityInvitationTwo->activity->location,
                'note' => $this->activityInvitationTwo->activity->note,
            ],
            'participant' => [
                'id' => $this->userParticipantTwo->participant->id,
                'program' => [
                    'id' => $this->userParticipantTwo->participant->program->id,
                    'name' => $this->userParticipantTwo->participant->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($invitationTwoResponse);
    }
    public function test_showAll_timeIntervalFromToFilter()
    {
        $from = (new DateTime('first day of this month'))->setTime(00, 00, 00)->format('Y-m-d H:i:s');
        $to = (new DateTime('last day of this month'))->setTime(00, 00, 00)->format('Y-m-d H:i:s');
        $uri = $this->activityInvitationUri
                . "?from=$from"
                . "&to=$to";
        
        $this->get($uri, $this->user->token)
                ->seeStatusCode(200);
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        $invitationResponse = [
            'id' => $this->activityInvitationOne->id,
        ];
        $this->seeJsonContains($invitationResponse);
    }
    public function test_showAll_cancelledStatusFilter()
    {
        $uri = $this->activityInvitationUri . "?cancelledStatus=true";
        $this->get($uri, $this->user->token)
                ->seeStatusCode(200);
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        $invitationResponse = [
            'id' => $this->activityInvitationTwo->id,
        ];
        $this->seeJsonContains($invitationResponse);
    }
    public function test_showAll_willAttendStatusesFilter()
    {
        $uri = $this->activityInvitationUri 
                . "?willAttendStatuses[]=%00";
//                . "&willAttendStatuses[]=true";
        $this->get($uri, $this->user->token)
                ->seeStatusCode(200);
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        $invitationOneResponse = [
            'id' => $this->activityInvitationOne->id,
        ];
        $this->seeJsonContains($invitationOneResponse);
//        $invitationTwoResponse = [
//            'id' => $this->activityInvitationTwo_team->id,
//        ];
//        $this->seeJsonContains($invitationTwoResponse);
    }

}
