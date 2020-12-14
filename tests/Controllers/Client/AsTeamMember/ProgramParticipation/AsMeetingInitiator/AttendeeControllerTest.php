<?php

namespace Tests\Controllers\Client\AsTeamMember\ProgramParticipation\AsMeetingInitiator;

use Tests\Controllers\RecordPreparation\{
    Firm\Client\RecordOfClientParticipant,
    Firm\Manager\RecordOfActivityInvitation,
    Firm\Program\Activity\RecordOfInvitee,
    Firm\Program\ActivityType\RecordOfActivityParticipant,
    Firm\Program\Consultant\RecordOfActivityInvitation as RecordOfActivityInvitation3,
    Firm\Program\Coordinator\RecordOfActivityInvitation as RecordOfActivityInvitation2,
    Firm\Program\Participant\RecordOfActivityInvitation as RecordOfActivityInvitation4,
    Firm\Program\RecordOfConsultant,
    Firm\Program\RecordOfCoordinator,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfClient,
    Firm\RecordOfManager,
    Firm\RecordOfPersonnel,
    Firm\RecordOfTeam,
    Firm\Team\RecordOfTeamProgramParticipation,
    RecordOfUser,
    User\RecordOfUserParticipant
};

class AttendeeControllerTest extends AsMeetingInitiatorTestCase
{

    protected $attendeeUri;
    protected $clientParticipant;
    protected $userParticipant;
    protected $teamParticipant;
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->attendeeUri = $this->asMeetingInitiatorUri . "/attendees";
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("User")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("UserParticipant")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();

        $activityType = $this->meeting->activityType;
        $program = $activityType->program;
        $firm = $program->firm;

        $personnel = new RecordOfPersonnel($firm, 0);
        $personnel->email = "purnama.adi+personnel@gmail.com";
        $personnelOne = new RecordOfPersonnel($firm, 1);
        $personnelOne->email = "purnama.adi+personnelOne@gmail.com";
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());
        $this->connection->table("Personnel")->insert($personnelOne->toArrayForDbEntry());

        $client = new RecordOfClient($firm, 0);
        $client->email = "purnama.adi+clientZero@gmail.com";
        $this->connection->table("Client")->insert($client->toArrayForDbEntry());

        $user = new RecordOfUser(0);
        $user->email = "purnama.adi+user@gmail.com";
        $this->connection->table("User")->insert($user->toArrayForDbEntry());

        $team = new RecordOfTeam($firm, $client, 0);
        $teamOne = new RecordOfTeam($firm, $client, 1);
        $this->connection->table("Team")->insert($team->toArrayForDbEntry());
        $this->connection->table("Team")->insert($teamOne->toArrayForDbEntry());

        $manager = new RecordOfManager($firm, 0, "manager@email.org", "Password123");
        $manager->email = "purnama.adi+manager@gmail.com";
        $this->managerOne = new RecordOfManager($firm, 1, "managerOne@email.org", "Password123");
        $this->managerOne->email = "purnama.adi+managerOne@gmail.com";
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

        $activityParticipant_coordinator = $this->meetingAttendace->invitee->activityParticipant;
        $activityParticipant_consultant = new RecordOfActivityParticipant($activityType, null, 0);
        $activityParticipant_consultant->participantType = "consultant";
        $activityParticipant_manager = new RecordOfActivityParticipant($activityType, null, 1);
        $activityParticipant_manager->participantType = "manager";
        $activityParticipant_coordinator = new RecordOfActivityParticipant($activityType, null, 2);
        $activityParticipant_coordinator->participantType = "coordinator";
        $this->connection->table("ActivityParticipant")->insert($activityParticipant_consultant->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($activityParticipant_manager->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($activityParticipant_coordinator->toArrayForDbEntry());

        $attendee_manager = new RecordOfInvitee($this->meeting, $activityParticipant_manager, 0);
        $attendee_coordinator = new RecordOfInvitee($this->meeting, $activityParticipant_coordinator, 1);
        $attendee_consultant = new RecordOfInvitee($this->meeting, $activityParticipant_consultant, 2);
        $attendee_clientParticipant = new RecordOfInvitee($this->meeting, $activityParticipant_coordinator, 3);
        $attendee_userParticipant = new RecordOfInvitee($this->meeting, $activityParticipant_coordinator, 4);
        $attendee_teamParticipant = new RecordOfInvitee($this->meeting, $activityParticipant_coordinator, 5);
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
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("User")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("UserParticipant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 6,
            "list" => [
                [
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
                ],
                [
                    "id" => $this->coordinatorAttendee->invitee->id,
                    "willAttend" => $this->coordinatorAttendee->invitee->willAttend,
                    "attended" => $this->coordinatorAttendee->invitee->attended,
                    "manager" => null,
                    "coordinator" => [
                         "id" => $this->coordinatorAttendee->coordinator->id,
                        "personnel" => [
                             "id" => $this->coordinatorAttendee->coordinator->personnel->id,
                             "name" => $this->coordinatorAttendee->coordinator->personnel->getFullName(),
                        ],
                    ],
                    "consultant" => null,
                    "participant" => null,
                ],
                [
                    "id" => $this->consultantAttendee->invitee->id,
                    "willAttend" => $this->consultantAttendee->invitee->willAttend,
                    "attended" => $this->consultantAttendee->invitee->attended,
                    "manager" => null,
                    "coordinator" => null,
                    "consultant" => [
                        "id" => $this->consultantAttendee->consultant->id,
                        "personnel" => [
                            "id" => $this->consultantAttendee->consultant->personnel->id,
                            "name" => $this->consultantAttendee->consultant->personnel->getFullName(),
                        ],
                    ],
                    "participant" => null,
                ],
                [
                    "id" => $this->clientParticipantAttendee->invitee->id,
                    "willAttend" => $this->clientParticipantAttendee->invitee->willAttend,
                    "attended" => $this->clientParticipantAttendee->invitee->attended,
                    "manager" => null,
                    "coordinator" => null,
                    "consultant" => null,
                    "participant" => [
                        "id" => $this->clientParticipantAttendee->participant->id,
                        "user" => null,
                        "client" => [
                            "id" => $this->clientParticipant->client->id,
                            "name" => $this->clientParticipant->client->getFullName(),
                        ],
                        "team" => null,
                    ],
                ],
                [
                    "id" => $this->userParticipantAttendee->invitee->id,
                    "willAttend" => $this->userParticipantAttendee->invitee->willAttend,
                    "attended" => $this->userParticipantAttendee->invitee->attended,
                    "manager" => null,
                    "coordinator" => null,
                    "consultant" => null,
                    "participant" => [
                        "id" => $this->userParticipantAttendee->participant->id,
                        "user" => [
                            "id" => $this->userParticipant->user->id,
                            "name" => $this->userParticipant->user->getFullName(),
                        ],
                        "client" => null,
                        "team" => null,
                    ],
                ],
                [
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
                ],
            ],
        ];
        
        $this->get($this->attendeeUri, $this->teamMember->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
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
        $this->get($uri, $this->teamMember->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_inviteManager_200()
    {
        $uri = $this->attendeeUri . "/invite-manager";
        $this->put($uri, $this->inviteManagerInput, $this->teamMember->client->token)
                ->seeStatusCode(200);
        
        $managerInviteeEntry = [
            "Manager_id" => $this->managerOne->id,
        ];
        $this->seeInDatabase("ManagerInvitee", $managerInviteeEntry);
    }
    
    public function test_inviteCoordinator_200()
    {
$this->disableExceptionHandling();
        $uri = $this->attendeeUri . "/invite-coordinator";
        $this->put($uri, $this->inviteCoordinatorInput, $this->teamMember->client->token)
                ->seeStatusCode(200);
        
        $coordinatorInviteeEntry = [
            "Coordinator_id" => $this->coordinatorOne->id,
        ];
        $this->seeInDatabase("CoordinatorInvitee", $coordinatorInviteeEntry);
    }
    
    public function test_inviteConsultant_200()
    {
        $uri = $this->attendeeUri . "/invite-consultant";
        $this->put($uri, $this->inviteConsultantInput, $this->teamMember->client->token)
                ->seeStatusCode(200);
        
        $consultantInviteeEntry = [
            "Consultant_id" => $this->consultantOne->id,
        ];
        $this->seeInDatabase("ConsultantInvitee", $consultantInviteeEntry);
    }
    
    public function test_inviteParticipant_200()
    {
        $uri = $this->attendeeUri . "/invite-participant";
        $this->put($uri, $this->inviteParticipantInput, $this->teamMember->client->token)
                ->seeStatusCode(200);
        
        $participantInviteeEntry = [
            "Participant_id" => $this->participantThree->id,
        ];
        $this->seeInDatabase("ParticipantInvitee", $participantInviteeEntry);
    }
    
    public function test_cancelInvitation_200()
    {
        $uri = $this->attendeeUri . "/cancel-invitation/{$this->userParticipantAttendee->invitee->id}";
        $this->patch($uri, [], $this->teamMember->client->token)
                ->seeStatusCode(200);
        
        $inviteeEntry = [
            "id" => $this->userParticipantAttendee->invitee->id,
            "cancelled" => true,
        ];
        $this->seeInDatabase("Invitee", $inviteeEntry);
        
    }

}
