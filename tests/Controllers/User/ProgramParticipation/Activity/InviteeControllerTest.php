<?php

namespace Tests\Controllers\User\ProgramParticipation\Activity;

use Tests\Controllers\ {
    RecordPreparation\Firm\Client\RecordOfClientParticipant,
    RecordPreparation\Firm\Manager\RecordOfActivityInvitation as RecordOfActivityInvitation2,
    RecordPreparation\Firm\Program\Activity\RecordOfInvitee,
    RecordPreparation\Firm\Program\Consultant\RecordOfActivityInvitation,
    RecordPreparation\Firm\Program\Coordinator\RecordOfActivityInvitation as RecordOfActivityInvitation3,
    RecordPreparation\Firm\Program\Participant\RecordOfActivityInvitation as RecordOfActivityInvitation4,
    RecordPreparation\Firm\Program\RecordOfConsultant,
    RecordPreparation\Firm\Program\RecordOfCoordinator,
    RecordPreparation\Firm\Program\RecordOfParticipant,
    RecordPreparation\Firm\RecordOfClient,
    RecordPreparation\Firm\RecordOfManager,
    RecordPreparation\Firm\RecordOfPersonnel,
    User\ProgramParticipation\ActivityTestCase
};

class InviteeControllerTest extends ActivityTestCase
{
    protected $inviteeUri;
    protected $consultantInvitation;
    protected $managerInvitation;
    protected $coordinatorInvitation;
    protected $participantInvitation;
    protected $clientParticipant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->inviteeUri = $this->activityUri . "/{$this->participantActivity->id}/invitees";
        
        
        $activity = $this->participantActivity->activity;
        $program = $activity->program;
        $firm = $program->firm;
        
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
        
        $invitation = new RecordOfInvitee($activity, $this->activityParticipant_consultant, 0);
        $invitationOne = new RecordOfInvitee($activity, $this->activityParticipantOne_Manager, 1);
        $invitationTwo = new RecordOfInvitee($activity, $this->activityParticipantTwo_Coordinator, 2);
        $invitationThree = new RecordOfInvitee($activity, $this->activityParticipantThree_Participant, 3);
        $this->connection->table("Invitee")->insert($invitation->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($invitationOne->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($invitationTwo->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($invitationThree->toArrayForDbEntry());
        
        $personnel = new RecordOfPersonnel($firm, 0);
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());
        
        $client = new RecordOfClient($firm, 0);
        $this->connection->table("Client")->insert($client->toArrayForDbEntry());
        
        $manager = new RecordOfManager($firm, 0, "manager@email.org", "Password123");
        $this->connection->table("Manager")->insert($manager->toArrayForDbEntry());
        
        $consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->connection->table("Consultant")->insert($consultant->toArrayForDbEntry());
        
        $coordinator = new RecordOfCoordinator($program, $personnel, 0);
        $this->connection->table("Coordinator")->insert($coordinator->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($program, 0);
        $this->connection->table("Participant")->insert($participant->toArrayForDbEntry());
        
        $this->clientParticipant = new RecordOfClientParticipant($client, $participant);
        $this->connection->table("ClientParticipant")->insert($this->clientParticipant->toArrayForDbEntry());
        
        $this->consultantInvitation = new RecordOfActivityInvitation($consultant, $invitation);
        $this->connection->table("ConsultantInvitee")->insert($this->consultantInvitation->toArrayForDbEntry());
        
        $this->managerInvitation = new RecordOfActivityInvitation2($manager, $invitationOne);
        $this->connection->table("ManagerInvitee")->insert($this->managerInvitation->toArrayForDbEntry());
        
        $this->coordinatorInvitation = new RecordOfActivityInvitation3($coordinator, $invitationTwo);
        $this->connection->table("CoordinatorInvitee")->insert($this->coordinatorInvitation->toArrayForDbEntry());
        
        $this->participantInvitation = new RecordOfActivityInvitation4($participant, $invitationThree);
        $this->connection->table("ParticipantInvitee")->insert($this->participantInvitation->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->consultantInvitation->invitee->id,
            "willAttend" => $this->consultantInvitation->invitee->willAttend,
            "attended" => $this->consultantInvitation->invitee->attended,
            "consultant" => [
                "id" => $this->consultantInvitation->consultant->id,
                "personnel" => [
                    "id" => $this->consultantInvitation->consultant->personnel->id,
                    "name" => $this->consultantInvitation->consultant->personnel->getFullName(),
                ],
            ],
            "manager" => null,
            "coordinator" => null,
            "participant" => null,
        ];
        
        $uri = $this->inviteeUri . "/{$this->consultantInvitation->invitee->id}";
        $this->get($uri, $this->programParticipation->user->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }

    public function test_showAll_200()
    {
        $response = [
            "total" => 4,
            "list" => [
                [
                    "id" => $this->consultantInvitation->invitee->id,
                    "willAttend" => $this->consultantInvitation->invitee->willAttend,
                    "attended" => $this->consultantInvitation->invitee->attended,
                    "consultant" => [
                        "id" => $this->consultantInvitation->consultant->id,
                        "personnel" => [
                            "id" => $this->consultantInvitation->consultant->personnel->id,
                            "name" => $this->consultantInvitation->consultant->personnel->getFullName(),
                        ],
                    ],
                    "manager" => null,
                    "coordinator" => null,
                    "participant" => null,
                ],
                [
                    "id" => $this->managerInvitation->invitee->id,
                    "willAttend" => $this->managerInvitation->invitee->willAttend,
                    "attended" => $this->managerInvitation->invitee->attended,
                    "consultant" => null,
                    "manager" => [
                        "id" => $this->managerInvitation->manager->id,
                        "name" => $this->managerInvitation->manager->name,
                    ],
                    "coordinator" => null,
                    "participant" => null,
                ],
                [
                    "id" => $this->coordinatorInvitation->invitee->id,
                    "willAttend" => $this->coordinatorInvitation->invitee->willAttend,
                    "attended" => $this->coordinatorInvitation->invitee->attended,
                    "consultant" => null,
                    "manager" => null,
                    "coordinator" => [
                        "id" => $this->coordinatorInvitation->coordinator->id,
                        "personnel" => [
                            "id" => $this->coordinatorInvitation->coordinator->personnel->id,
                            "name" => $this->coordinatorInvitation->coordinator->personnel->getFullName(),
                        ],
                    ],
                    "participant" => null,
                ],
                [
                    "id" => $this->participantInvitation->invitee->id,
                    "willAttend" => $this->participantInvitation->invitee->willAttend,
                    "attended" => $this->participantInvitation->invitee->attended,
                    "consultant" => null,
                    "manager" => null,
                    "coordinator" => null,
                    "participant" => [
                        "id" => $this->participantInvitation->participant->id,
                        "user" => null,
                        "client" => [
                            "id" => $this->clientParticipant->client->id,
                            "name" => $this->clientParticipant->client->getFullName(),
                        ],
                        "team" => null,
                    ],
                ],
            ],
        ];
        
        $this->get($this->inviteeUri, $this->programParticipation->user->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
 