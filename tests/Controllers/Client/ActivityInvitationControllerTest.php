<?php

namespace Tests\Controllers\Client;

use DateTime;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\RecordOfInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfActivityInvitation;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivity;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivityType;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;

class ActivityInvitationControllerTest extends ClientTestCase
{

    protected $activityInvitationUri;
    protected $activityInvitationOne_client;
    protected $activityInvitationTwo_team;
    protected $activityInvitationThree_teamInactiveMember;
    protected $clientParticipantOne;
    protected $teamParticipantTwo;
    protected $teamParticipantThree_inactiveTeamMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activityInvitationUri = $this->clientUri . "/activity-invitations";
        
        $this->connection->table('Program')->truncate();
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ParticipantInvitee')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('TeamParticipant')->truncate();

        $firm = $this->client->firm;
        
        $programOne = new RecordOfProgram($firm, '1');
        $programTwo = new RecordOfProgram($firm, '2');
        $programThree = new RecordOfProgram($firm, '3');
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

        $this->activityInvitationOne_client = new RecordOfInvitee($activityOne, $activityParticipantOne, '1');
        $this->activityInvitationTwo_team = new RecordOfInvitee($activityTwo, $activityParticipantTwo, '2');
        $this->activityInvitationTwo_team->cancelled = true;
        $this->activityInvitationTwo_team->willAttend = true;
        $this->activityInvitationThree_teamInactiveMember = new RecordOfInvitee($activityThree, $activityParticipantThree, '3');
        $this->connection->table('Invitee')->insert($this->activityInvitationOne_client->toArrayForDbEntry());
        $this->connection->table('Invitee')->insert($this->activityInvitationTwo_team->toArrayForDbEntry());
        $this->connection->table('Invitee')->insert($this->activityInvitationThree_teamInactiveMember->toArrayForDbEntry());

        $participantOne = new RecordOfParticipant($programOne, '1');
        $participantTwo = new RecordOfParticipant($programTwo, '2');
        $participantThree = new RecordOfParticipant($programThree, '3');
        $this->connection->table('Participant')->insert($participantOne->toArrayForDbEntry());
        $this->connection->table('Participant')->insert($participantTwo->toArrayForDbEntry());
        $this->connection->table('Participant')->insert($participantThree->toArrayForDbEntry());
        
        $participantInviteeOne = new RecordOfActivityInvitation($participantOne, $this->activityInvitationOne_client);
        $participantInviteeTwo = new RecordOfActivityInvitation($participantTwo, $this->activityInvitationTwo_team);
        $participantInviteeThree = new RecordOfActivityInvitation($participantThree, $this->activityInvitationThree_teamInactiveMember);
        $this->connection->table('ParticipantInvitee')->insert($participantInviteeOne->toArrayForDbEntry());
        $this->connection->table('ParticipantInvitee')->insert($participantInviteeTwo->toArrayForDbEntry());
        $this->connection->table('ParticipantInvitee')->insert($participantInviteeThree->toArrayForDbEntry());

        $this->clientParticipantOne = new RecordOfClientParticipant($this->client, $participantOne);
        $this->connection->table('ClientParticipant')->insert($this->clientParticipantOne->toArrayForDbEntry());

        $teamTwo = new RecordOfTeam($firm, $this->client, '2');
        $teamThree = new RecordOfTeam($firm, $this->client, '3');
        $this->connection->table('Team')->insert($teamTwo->toArrayForDbEntry());
        $this->connection->table('Team')->insert($teamThree->toArrayForDbEntry());

        $memberTwo = new RecordOfMember($teamTwo, $this->client, '2');
        $memberThree = new RecordOfMember($teamThree, $this->client, '3');
        $memberThree->active = false;
        $this->connection->table('T_Member')->insert($memberTwo->toArrayForDbEntry());
        $this->connection->table('T_Member')->insert($memberThree->toArrayForDbEntry());
        
        $this->teamParticipantTwo = new RecordOfTeamProgramParticipation($teamTwo, $participantTwo);
        $this->teamParticipantThree_inactiveTeamMember = new RecordOfTeamProgramParticipation($teamThree, $participantThree);
        $this->connection->table('TeamParticipant')->insert($this->teamParticipantTwo->toArrayForDbEntry());
        $this->connection->table('TeamParticipant')->insert($this->teamParticipantThree_inactiveTeamMember->toArrayForDbEntry());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ParticipantInvitee')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
    }
    
    public function test_showAll_200()
    {
        $this->get($this->activityInvitationUri, $this->client->token)
                ->seeStatusCode(200);
        
        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);
        
        $invitationOneResponse = [
            'id' => $this->activityInvitationOne_client->id,
            'anInitiator' => $this->activityInvitationOne_client->anInitiator,
            'willAttend' => $this->activityInvitationOne_client->willAttend,
            'cancelled' => $this->activityInvitationOne_client->cancelled,
            'reportSubmitted' => false,
            'activity' => [
                'id' => $this->activityInvitationOne_client->activity->id,
                'name' => $this->activityInvitationOne_client->activity->name,
                'description' => $this->activityInvitationOne_client->activity->description,
                'startTime' => $this->activityInvitationOne_client->activity->startDateTime,
                'endTime' => $this->activityInvitationOne_client->activity->endDateTime,
                'location' => $this->activityInvitationOne_client->activity->location,
                'note' => $this->activityInvitationOne_client->activity->note,
            ],
            'participant' => [
                'id' => $this->clientParticipantOne->participant->id,
                'program' => [
                    'id' => $this->clientParticipantOne->participant->program->id,
                    'name' => $this->clientParticipantOne->participant->program->name,
                ],
                'team' => null
            ],
        ];
        $this->seeJsonContains($invitationOneResponse);
        $invitationTwoResponse = [
            'id' => $this->activityInvitationTwo_team->id,
            'anInitiator' => $this->activityInvitationTwo_team->anInitiator,
            'willAttend' => $this->activityInvitationTwo_team->willAttend,
            'cancelled' => $this->activityInvitationTwo_team->cancelled,
            'reportSubmitted' => false,
            'activity' => [
                'id' => $this->activityInvitationTwo_team->activity->id,
                'name' => $this->activityInvitationTwo_team->activity->name,
                'description' => $this->activityInvitationTwo_team->activity->description,
                'startTime' => $this->activityInvitationTwo_team->activity->startDateTime,
                'endTime' => $this->activityInvitationTwo_team->activity->endDateTime,
                'location' => $this->activityInvitationTwo_team->activity->location,
                'note' => $this->activityInvitationTwo_team->activity->note,
            ],
            'participant' => [
                'id' => $this->teamParticipantTwo->participant->id,
                'program' => [
                    'id' => $this->teamParticipantTwo->participant->program->id,
                    'name' => $this->teamParticipantTwo->participant->program->name,
                ],
                'team' => [
                    'id' => $this->teamParticipantTwo->team->id,
                    'name' => $this->teamParticipantTwo->team->name,
                ]
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
        
        $this->get($uri, $this->client->token)
                ->seeStatusCode(200);
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        $invitationResponse = [
            'id' => $this->activityInvitationOne_client->id,
        ];
        $this->seeJsonContains($invitationResponse);
    }
    public function test_showAll_cancelledStatusFilter()
    {
        $uri = $this->activityInvitationUri . "?cancelledStatus=true";
        $this->get($uri, $this->client->token)
                ->seeStatusCode(200);
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        $invitationResponse = [
            'id' => $this->activityInvitationTwo_team->id,
        ];
        $this->seeJsonContains($invitationResponse);
    }
    public function test_showAll_willAttendStatusesFilter()
    {
        $uri = $this->activityInvitationUri 
                . "?willAttendStatuses[]=%00";
//                . "&willAttendStatuses[]=true";
        $this->get($uri, $this->client->token)
                ->seeStatusCode(200);
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        $invitationOneResponse = [
            'id' => $this->activityInvitationOne_client->id,
        ];
        $this->seeJsonContains($invitationOneResponse);
//        $invitationTwoResponse = [
//            'id' => $this->activityInvitationTwo_team->id,
//        ];
//        $this->seeJsonContains($invitationTwoResponse);
    }

}
