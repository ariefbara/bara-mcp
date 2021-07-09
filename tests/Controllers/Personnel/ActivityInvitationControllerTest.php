<?php

namespace Tests\Controllers\Personnel;

use DateTime;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\RecordOfInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfActivityInvitation;
use Tests\Controllers\RecordPreparation\Firm\Program\Coordinator\RecordOfActivityInvitation as RecordOfActivityInvitation2;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivity;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivityType;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;

class ActivityInvitationControllerTest extends PersonnelTestCase
{
    protected $activityInvitationUri;
    protected $activityInvitationOne;
    protected $activityInvitationTwo;
    protected $activityInvitationThree;
    protected $mentorInviteeOne;
    protected $coordinatorInviteeTwo;
    protected $mentorInviteeThree;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activityInvitationUri = $this->personnelUri . "/activity-invitations";
        
        $this->connection->table('Program')->truncate();
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultantInvitee')->truncate();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('CoordinatorInvitee')->truncate();
        
        $firm = $this->personnel->firm;
        
        $programOne = new RecordOfProgram($firm, '1');
        $programTwo = new RecordOfProgram($firm, '2');
        $programThree = new RecordOfProgram($firm, '3');
        $this->connection->table('Program')->insert($programOne->toArrayForDbEntry());
        $this->connection->table('Program')->insert($programTwo->toArrayForDbEntry());
        $this->connection->table('Program')->insert($programThree->toArrayForDbEntry());
        
        $activityType = new RecordOfActivityType($programOne, '1');
        $this->connection->table('ActivityType')->insert($activityType->toArrayForDbEntry());
        
        $activityParticipant = new RecordOfActivityParticipant($activityType, null, '1');
        $this->connection->table('ActivityParticipant')->insert($activityParticipant->toArrayForDbEntry());
        
        $activityOne = new RecordOfActivity($activityType, '1');
        $activityOne->startDateTime = (new DateTime(''))->format('Y-m-d H:i:s');
        $activityTwo = new RecordOfActivity($activityType, '2');
        $activityTwo->startDateTime = (new DateTime('+1 months'))->format('Y-m-d H:i:s');
        $activityThree = new RecordOfActivity($activityType, '3');
        $activityThree->startDateTime = (new DateTime('+2 months'))->format('Y-m-d H:i:s');
        $this->connection->table('Activity')->insert($activityOne->toArrayForDbEntry());
        $this->connection->table('Activity')->insert($activityTwo->toArrayForDbEntry());
        $this->connection->table('Activity')->insert($activityThree->toArrayForDbEntry());
        
        $this->activityInvitationOne = new RecordOfInvitee($activityOne, $activityParticipant, '1');
        $this->activityInvitationTwo = new RecordOfInvitee($activityTwo, $activityParticipant, '2');
        $this->activityInvitationTwo->willAttend = true;
        $this->activityInvitationThree = new RecordOfInvitee($activityThree, $activityParticipant, '3');
        $this->activityInvitationThree->cancelled = true;
        $this->activityInvitationThree->willAttend = false;
        $this->connection->table('Invitee')->insert($this->activityInvitationOne->toArrayForDbEntry());
        $this->connection->table('Invitee')->insert($this->activityInvitationTwo->toArrayForDbEntry());
        $this->connection->table('Invitee')->insert($this->activityInvitationThree->toArrayForDbEntry());
        
        $consultantOne = new RecordOfConsultant($programOne, $this->personnel, '1');
        $consultantThree = new RecordOfConsultant($programThree, $this->personnel, '3');
        $this->connection->table('Consultant')->insert($consultantOne->toArrayForDbEntry());
        $this->connection->table('Consultant')->insert($consultantThree->toArrayForDbEntry());
        
        $this->mentorInviteeOne = new RecordOfActivityInvitation($consultantOne, $this->activityInvitationOne);
        $this->mentorInviteeThree = new RecordOfActivityInvitation($consultantThree, $this->activityInvitationThree);
        $this->connection->table('ConsultantInvitee')->insert($this->mentorInviteeOne->toArrayForDbEntry());
        $this->connection->table('ConsultantInvitee')->insert($this->mentorInviteeThree->toArrayForDbEntry());
        
        $coordinatorTwo = new RecordOfCoordinator($programTwo, $this->personnel, '2');
        $this->connection->table('Coordinator')->insert($coordinatorTwo->toArrayForDbEntry());
        
        $this->coordinatorInviteeTwo = new RecordOfActivityInvitation2($coordinatorTwo, $this->activityInvitationTwo);
        $this->connection->table('CoordinatorInvitee')->insert($this->coordinatorInviteeTwo->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultantInvitee')->truncate();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('CoordinatorInvitee')->truncate();
    }
    
    public function test_showAll_200()
    {
        $uri = $this->activityInvitationUri;
        $this->get($uri, $this->personnel->token)
                ->seeStatusCode(200);
        
        $response = [
            'total' => 3,
            'list' => [
                [
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
                    'consultant' => [
                        'id' => $this->mentorInviteeOne->consultant->id,
                        'program' => [
                            'id' => $this->mentorInviteeOne->consultant->program->id,
                            'name' => $this->mentorInviteeOne->consultant->program->name,
                        ],
                    ],
                    'coordinator' => null,
                ],
                [
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
                    'coordinator' => [
                        'id' => $this->coordinatorInviteeTwo->coordinator->id,
                        'program' => [
                            'id' => $this->coordinatorInviteeTwo->coordinator->program->id,
                            'name' => $this->coordinatorInviteeTwo->coordinator->program->name,
                        ],
                    ],
                    'consultant' => null,
                ],
                [
                    'id' => $this->activityInvitationThree->id,
                    'anInitiator' => $this->activityInvitationThree->anInitiator,
                    'willAttend' => $this->activityInvitationThree->willAttend,
                    'cancelled' => $this->activityInvitationThree->cancelled,
                    'reportSubmitted' => false,
                    'activity' => [
                        'id' => $this->activityInvitationThree->activity->id,
                        'name' => $this->activityInvitationThree->activity->name,
                        'description' => $this->activityInvitationThree->activity->description,
                        'startTime' => $this->activityInvitationThree->activity->startDateTime,
                        'endTime' => $this->activityInvitationThree->activity->endDateTime,
                        'location' => $this->activityInvitationThree->activity->location,
                        'note' => $this->activityInvitationThree->activity->note,
                        
                    ],
                    'consultant' => [
                        'id' => $this->mentorInviteeThree->consultant->id,
                        'program' => [
                            'id' => $this->mentorInviteeThree->consultant->program->id,
                            'name' => $this->mentorInviteeThree->consultant->program->name,
                        ],
                    ],
                    'coordinator' => null,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_cancelledFilter_200()
    {
        $uri = $this->activityInvitationUri . "?cancelled=false";
        $this->get($uri, $this->personnel->token)
                ->seeStatusCode(200);
        $totalResponse = ['total' => 2];
        $inviteeOneResponse = [
            'id' => $this->activityInvitationOne->id,
        ];
        $this->seeJsonContains($inviteeOneResponse);
        $inviteeTwoResponse = [
            'id' => $this->activityInvitationTwo->id,
        ];
        $this->seeJsonContains($inviteeTwoResponse);
    }
    public function test_showAll_timeIntervalFilter_200()
    {
$this->disableExceptionHandling();
        $startOfMonth = (new \DateTime('first day of this month'))->setTime(00, 00, 00)->format('Y-m-d H:i:s');
        $endOfMonth = (new \DateTime('last day of this month'))->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        $uri = $this->activityInvitationUri 
                . "?from={$startOfMonth}"
                . "&to={$endOfMonth}";
        $this->get($uri, $this->personnel->token)
                ->seeStatusCode(200);
        $totalResponse = ['total' => 1];
        $inviteeOneResponse = [
            'id' => $this->activityInvitationOne->id,
        ];
        $this->seeJsonContains($inviteeOneResponse);
    }
    public function test_showAll_willAttendStatusesFilter_200()
    {
        $uri = $this->activityInvitationUri 
                . "?willAttendStatuses[]=true"
                . "&willAttendStatuses[]=%00";//%00 repsesent NULL value
        $this->get($uri, $this->personnel->token)
                ->seeStatusCode(200);
        $totalResponse = ['total' => 2];
        $inviteeOneResponse = [
            'id' => $this->activityInvitationOne->id,
        ];
        $this->seeJsonContains($inviteeOneResponse);
        $inviteeTwoResponse = [
            'id' => $this->activityInvitationTwo->id,
        ];
        $this->seeJsonContains($inviteeTwoResponse);
    }
}
