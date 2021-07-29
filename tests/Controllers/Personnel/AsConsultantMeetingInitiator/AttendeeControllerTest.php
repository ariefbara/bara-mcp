<?php

namespace Tests\Controllers\Personnel\AsConsultantMeetingInitiator;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Manager\RecordOfActivityInvitation;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\RecordOfInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfActivityInvitation as RecordOfActivityInvitation3;
use Tests\Controllers\RecordPreparation\Firm\Program\Coordinator\RecordOfActivityInvitation as RecordOfActivityInvitation2;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfActivityInvitation as RecordOfActivityInvitation4;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDedicatedMentor;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfManager;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class AttendeeControllerTest extends AsMeetingInitiatorTestCase
{

    protected $attendeeUri;
    protected $clientParticipant;
    protected $userParticipant;
    protected $teamParticipant;
    protected $teamParticipantThree;
    protected $managerAttendee;
    protected $coordinatorAttendee;
    protected $consultantAttendee;
    protected $clientParticipantAttendee;
    protected $userParticipantAttendee;
    protected $teamParticipantAttendee;
    
    protected $managerOne;
    protected $coordinatorOne;
    protected $consultantOne;
    protected $participantThree;
    
    protected $inviteManagerInput;
    protected $inviteCoordinatorInput;
    protected $inviteConsultantInput;
    protected $inviteParticipantInput;
    
    protected $dedicatedMentorOne;
    protected $dedicatedMentorTwo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->attendeeUri = $this->asMeetingInitiatorUri . "/attendees";
        $this->connection->table("Client")->truncate();
        $this->connection->table("User")->truncate();
        $this->connection->table("Team")->truncate();
        $this->connection->table("T_Member")->truncate();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("DedicatedMentor")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("UserParticipant")->truncate();
        $this->connection->table("TeamParticipant")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();

        $activityType = $this->meeting->activityType;
        $firm = $this->personnel->firm;
        $program = $activityType->program;

        $personnel = new RecordOfPersonnel($firm, 0);
        $personnelOne = new RecordOfPersonnel($firm, 1);
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());
        $this->connection->table("Personnel")->insert($personnelOne->toArrayForDbEntry());

        $client = new RecordOfClient($firm, 0);
        $this->connection->table("Client")->insert($client->toArrayForDbEntry());

        $user = new RecordOfUser(0);
        $this->connection->table("User")->insert($user->toArrayForDbEntry());

        $team = new RecordOfTeam($firm, $client, 0);
        $teamOne = new RecordOfTeam($firm, $client, 1);
        $this->connection->table("Team")->insert($team->toArrayForDbEntry());
        $this->connection->table("Team")->insert($teamOne->toArrayForDbEntry());
        
        $member = new RecordOfMember($team, $client, 0);
        $this->connection->table("T_Member")->insert($member->toArrayForDbEntry());

        $manager = new RecordOfManager($firm, 0);
        $this->managerOne = new RecordOfManager($firm, 1);
        $this->connection->table("Manager")->insert($manager->toArrayForDbEntry());
        $this->connection->table("Manager")->insert($this->managerOne->toArrayForDbEntry());

        $coordinator = new RecordOfCoordinator($program, $personnel, 0);
        $this->coordinatorOne = new RecordOfCoordinator($program, $personnelOne, 1);
        $this->connection->table("Coordinator")->insert($coordinator->toArrayForDbEntry());
        $this->connection->table("Coordinator")->insert($this->coordinatorOne->toArrayForDbEntry());

        $consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->consultantOne = new RecordOfConsultant($program, $personnelOne, 1);
        $this->connection->table("Consultant")->insert($consultant->toArrayForDbEntry());
        $this->connection->table("Consultant")->insert($this->consultantOne->toArrayForDbEntry());

        $participant = new RecordOfParticipant($program, 0);
        $participantOne = new RecordOfParticipant($program, 1);
        $participantTwo = new RecordOfParticipant($program, 2);
        $this->participantThree = new RecordOfParticipant($program, 3);
        $this->connection->table("Participant")->insert($participant->toArrayForDbEntry());
        $this->connection->table("Participant")->insert($participantOne->toArrayForDbEntry());
        $this->connection->table("Participant")->insert($participantTwo->toArrayForDbEntry());
        $this->connection->table("Participant")->insert($this->participantThree->toArrayForDbEntry());

        $this->clientParticipant = new RecordOfClientParticipant($client, $this->participantThree);
        $this->connection->table("ClientParticipant")->insert($this->clientParticipant->toArrayForDbEntry());

        $this->userParticipant = new RecordOfUserParticipant($user, $participantOne);
        $this->connection->table("UserParticipant")->insert($this->userParticipant->toArrayForDbEntry());

        $this->teamParticipant = new RecordOfTeamProgramParticipation($team, $participantTwo);
        $this->teamParticipantThree = new RecordOfTeamProgramParticipation($teamOne, $participant);
        $this->connection->table("TeamParticipant")->insert($this->teamParticipant->toArrayForDbEntry());
        $this->connection->table("TeamParticipant")->insert($this->teamParticipantThree->toArrayForDbEntry());

        $activityParticipant_consultant = new RecordOfActivityParticipant($activityType, null, 0);
        $activityParticipant_consultant->participantType = "consultant";
        $activityParticipant_manager = new RecordOfActivityParticipant($activityType, null, 1);
        $activityParticipant_manager->participantType = 'manager';
        $activityParticipant_participant = new RecordOfActivityParticipant($activityType, null, 2);
        $activityParticipant_participant->participantType = "participant";
        $this->connection->table("ActivityParticipant")->insert($activityParticipant_consultant->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($activityParticipant_manager->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($activityParticipant_participant->toArrayForDbEntry());

        $attendee_manager = new RecordOfInvitee($this->meeting, $activityParticipant_manager, 0);
        $attendee_coordinator = new RecordOfInvitee($this->meeting, $activityParticipant_manager, 1);
        $attendee_consultant = new RecordOfInvitee($this->meeting, $activityParticipant_consultant, 2);
        $attendee_clientParticipant = new RecordOfInvitee($this->meeting, $activityParticipant_participant, 3);
        $attendee_userParticipant = new RecordOfInvitee($this->meeting, $activityParticipant_participant, 4);
        $attendee_teamParticipant = new RecordOfInvitee($this->meeting, $activityParticipant_participant, 5);
        $this->connection->table("Invitee")->insert($attendee_manager->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($attendee_coordinator->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($attendee_consultant->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($attendee_clientParticipant->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($attendee_userParticipant->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($attendee_teamParticipant->toArrayForDbEntry());


        $this->managerAttendee = new RecordOfActivityInvitation($manager, $attendee_manager);
        $this->connection->table("ManagerInvitee")->insert($this->managerAttendee->toArrayForDbEntry());

        $this->coordinatorAttendee = new RecordOfActivityInvitation2($coordinator, $attendee_coordinator);
        $this->connection->table("CoordinatorInvitee")->insert($this->coordinatorAttendee->toArrayForDbEntry());

        $this->consultantAttendee = new RecordOfActivityInvitation3($consultant, $attendee_consultant);
        $this->connection->table("ConsultantInvitee")->insert($this->consultantAttendee->toArrayForDbEntry());

        $this->clientParticipantAttendee = new RecordOfActivityInvitation4($participant, $attendee_clientParticipant);
        $this->userParticipantAttendee = new RecordOfActivityInvitation4($participantOne, $attendee_userParticipant);
        $this->teamParticipantAttendee = new RecordOfActivityInvitation4($participantTwo, $attendee_teamParticipant);
        $this->connection->table("ParticipantInvitee")->insert($this->clientParticipantAttendee->toArrayForDbEntry());
        $this->connection->table("ParticipantInvitee")->insert($this->userParticipantAttendee->toArrayForDbEntry());
        $this->connection->table("ParticipantInvitee")->insert($this->teamParticipantAttendee->toArrayForDbEntry());
        
        $this->dedicatedMentorOne = new RecordOfDedicatedMentor($this->clientParticipant->participant, $this->consultant, '1');
        $this->dedicatedMentorTwo = new RecordOfDedicatedMentor($this->userParticipant->participant, $this->consultant, '2');
        
        $this->inviteManagerInput = [
            "managerId" => $this->managerOne->id,
        ];
        $this->inviteCoordinatorInput = [
            "coordinatorId" => $this->coordinatorOne->id,
        ];
        $this->inviteConsultantInput = [
            "consultantId" => $this->consultantOne->id,
        ];
        $this->inviteParticipantInput = [
            "participantId" => $this->participantThree->id,
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Client")->truncate();
        $this->connection->table("User")->truncate();
        $this->connection->table("Team")->truncate();
        $this->connection->table("T_Member")->truncate();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("DedicatedMentor")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("UserParticipant")->truncate();
        $this->connection->table("TeamParticipant")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
    }
    
    public function test_showAll_200()
    {
        $totalResponse = ["total" => 6];
        $managerAttendeeResponse = [
            "id" => $this->managerAttendee->invitee->id,
            "willAttend" => $this->managerAttendee->invitee->willAttend,
            "attended" => $this->managerAttendee->invitee->attended,
            "manager" => [
                "id" => $this->managerAttendee->manager->id,
                "name" => $this->managerAttendee->manager->name,
            ],
            "coordinator" => null,
            "consultant" => null,
            "participant" => null,
        ];
        $attendeeListResponse = [
            "id" => $this->coordinatorAttendee->invitee->id,
            "id" => $this->consultantAttendee->invitee->id,
            "id" => $this->clientParticipantAttendee->invitee->id,
            "id" => $this->userParticipantAttendee->invitee->id,
            "id" => $this->teamParticipantAttendee->invitee->id,
        ];
        $this->get($this->attendeeUri, $this->personnel->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($managerAttendeeResponse)
                ->seeJsonContains($attendeeListResponse)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveInitiator_403()
    {
        $this->setInactiveMeetingInitiator();
        $this->get($this->attendeeUri, $this->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->teamParticipantAttendee->invitee->id,
            "willAttend" => $this->teamParticipantAttendee->invitee->willAttend,
            "attended" => $this->teamParticipantAttendee->invitee->attended,
            "manager" => null,
            "coordinator" => null,
            "consultant" => null,
            "participant" => [
                "id" => $this->teamParticipantAttendee->participant->id,
                "user" => null,
                "client" => null,
                "team" => [
                    "id" => $this->teamParticipant->team->id,
                    "name" => $this->teamParticipant->team->name,
                ], 
            ],
        ];
        $uri = $this->attendeeUri . "/{$this->teamParticipantAttendee->invitee->id}";
        $this->get($uri, $this->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_inactiveInitiator_403()
    {
        $this->setInactiveMeetingInitiator();
        $uri = $this->attendeeUri . "/{$this->teamParticipantAttendee->invitee->id}";
        $this->get($uri, $this->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_inviteManager_200()
    {
        $uri = $this->attendeeUri . "/invite-manager";
        $this->put($uri, $this->inviteManagerInput, $this->personnel->token)
                ->seeStatusCode(200);
        
        $managerInviteeEntry = [
            "Manager_id" => $this->managerOne->id,
        ];
        $this->seeInDatabase("ManagerInvitee", $managerInviteeEntry);
    }
    public function test_inviteManager_inactiveInitiator_403()
    {
        $this->setInactiveMeetingInitiator();
        $uri = $this->attendeeUri . "/invite-manager";
        $this->put($uri, $this->inviteManagerInput, $this->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_inviteCoordinator_200()
    {
        $uri = $this->attendeeUri . "/invite-coordinator";
        $this->put($uri, $this->inviteCoordinatorInput, $this->personnel->token)
                ->seeStatusCode(200);
        
        $coordinatorInviteeEntry = [
            "Coordinator_id" => $this->coordinatorOne->id,
        ];
        $this->seeInDatabase("CoordinatorInvitee", $coordinatorInviteeEntry);
    }
    public function test_inviteCoordinator_inactiveCoordinator_403()
    {
        $this->connection->table("Coordinator")->truncate();
        $this->coordinatorOne->active = false;
        $this->connection->table("Coordinator")->insert($this->coordinatorOne->toArrayForDbEntry());
        
        $uri = $this->attendeeUri . "/invite-coordinator";
        $this->put($uri, $this->inviteCoordinatorInput, $this->personnel->token)
                ->seeStatusCode(403);
    }
    public function test_inviteCoordinator_inactiveInitiator_403()
    {
        $this->setInactiveMeetingInitiator();
        $uri = $this->attendeeUri . "/invite-coordinator";
        $this->put($uri, $this->inviteCoordinatorInput, $this->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_inviteConsultant_200()
    {
        $uri = $this->attendeeUri . "/invite-consultant";
        $this->put($uri, $this->inviteConsultantInput, $this->personnel->token)
                ->seeStatusCode(200);
        
        $consultantInviteeEntry = [
            "Consultant_id" => $this->consultantOne->id,
        ];
        $this->seeInDatabase("ConsultantInvitee", $consultantInviteeEntry);
    }
    public function test_inviteConsultant_inactiveConsultant_403()
    {
        $this->connection->table("Consultant")->truncate();
        $this->consultantOne->active = false;
        $this->connection->table("Consultant")->insert($this->consultant->toArrayForDbEntry());
        $this->connection->table("Consultant")->insert($this->consultantOne->toArrayForDbEntry());
        
        $uri = $this->attendeeUri . "/invite-consultant";
        $this->put($uri, $this->inviteConsultantInput, $this->personnel->token)
                ->seeStatusCode(403);
    }
    public function test_inviteConsultant_inactiveInitiator_403()
    {
        $this->setInactiveMeetingInitiator();
        $uri = $this->attendeeUri . "/invite-consultant";
        $this->put($uri, $this->inviteConsultantInput, $this->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_inviteClientParticipant_200()
    {
        $uri = $this->attendeeUri . "/invite-participant";
        $this->put($uri, $this->inviteParticipantInput, $this->personnel->token)
                ->seeStatusCode(200);
        
        $participantInviteeEntry = [
            "Participant_id" => $this->participantThree->id,
        ];
        $this->seeInDatabase("ParticipantInvitee", $participantInviteeEntry);
    }
    public function test_inviteUserParticipant_200()
    {
        $this->connection->table("ClientParticipant")->truncate();
        $userParticipant = new RecordOfUserParticipant($this->userParticipant->user, $this->participantThree);
        $this->connection->table("UserParticipant")->insert($userParticipant->toArrayForDbEntry());
        
        $uri = $this->attendeeUri . "/invite-participant";
        $this->put($uri, $this->inviteParticipantInput, $this->personnel->token)
                ->seeStatusCode(200);
        
        $participantInviteeEntry = [
            "Participant_id" => $this->inviteParticipantInput["participantId"],
        ];
        $this->seeInDatabase("ParticipantInvitee", $participantInviteeEntry);
    }
    public function test_inviteTeamParticipant_200()
    {
        $this->connection->table("ClientParticipant")->truncate();
        $teamParticipant = new RecordOfTeamProgramParticipation($this->teamParticipant->team, $this->participantThree);
        $this->connection->table("TeamParticipant")->insert($teamParticipant->toArrayForDbEntry());
        
        $uri = $this->attendeeUri . "/invite-participant";
        $this->put($uri, $this->inviteParticipantInput, $this->personnel->token)
                ->seeStatusCode(200);
        
        $participantInviteeEntry = [
            "Participant_id" => $this->inviteParticipantInput["participantId"],
        ];
        $this->seeInDatabase("ParticipantInvitee", $participantInviteeEntry);
    }
    public function test_inviteParticipant_inactiveInitiator_403()
    {
        $this->setInactiveMeetingInitiator();
        $uri = $this->attendeeUri . "/invite-participant";
        $this->put($uri, $this->inviteParticipantInput, $this->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_inviteAllDedicatedMentees_200()
    {
        $this->connection->table('ParticipantInvitee')->truncate();
        
        $this->dedicatedMentorOne->insert($this->connection);
        $this->dedicatedMentorTwo->insert($this->connection);
        
        $uri = $this->attendeeUri . "/invite-all-active-dedicated-mentees";
        $this->put($uri, $this->inviteParticipantInput, $this->personnel->token)
                ->seeStatusCode(200);
        
        $dedicatedMenteeInvititationOneEntry = ['Participant_id' => $this->userParticipant->participant->id];
        $this->seeInDatabase('ParticipantInvitee', $dedicatedMenteeInvititationOneEntry);
        
        $dedicatedMenteeInvititationTwoEntry = ['Participant_id' => $this->clientParticipant->participant->id];
        $this->seeInDatabase('ParticipantInvitee', $dedicatedMenteeInvititationTwoEntry);
    }
    
    public function test_cancelInvitation_200()
    {
        $uri = $this->attendeeUri . "/cancel-invitation/{$this->userParticipantAttendee->invitee->id}";
        $this->patch($uri, [], $this->personnel->token)
                ->seeStatusCode(200);
        
        $inviteeEntry = [
            "id" => $this->userParticipantAttendee->invitee->id,
            "cancelled" => true,
        ];
        $this->seeInDatabase("Invitee", $inviteeEntry);
        
    }
    public function test_cancelInvitation_inactiveInitiator_403()
    {
        $this->setInactiveMeetingInitiator();
        $this->connection->table("Invitee")->insert($this->teamParticipantAttendee->invitee->toArrayForDbEntry());
        
        $uri = $this->attendeeUri . "/cancel-invitation/{$this->teamParticipantAttendee->invitee->id}";
        $this->patch($uri, [], $this->personnel->token)
                ->seeStatusCode(403);
    }

}
