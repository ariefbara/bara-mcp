<?php

namespace Tests\Controllers\Manager\Activity;

use Tests\Controllers\ {
    Manager\ActivityTestCase,
    RecordPreparation\Firm\Client\RecordOfClientParticipant,
    RecordPreparation\Firm\Manager\RecordOfActivityInvitation as ManagerInvitee,
    RecordPreparation\Firm\Program\Activity\RecordOfInvitee,
    RecordPreparation\Firm\Program\Consultant\RecordOfActivityInvitation as ConsultantInvitee,
    RecordPreparation\Firm\Program\Coordinator\RecordOfActivityInvitation as CoordinatorInvitee,
    RecordPreparation\Firm\Program\Participant\RecordOfActivityInvitation as ParticipantInvitee,
    RecordPreparation\Firm\Program\RecordOfConsultant,
    RecordPreparation\Firm\Program\RecordOfCoordinator,
    RecordPreparation\Firm\Program\RecordOfParticipant,
    RecordPreparation\Firm\RecordOfClient,
    RecordPreparation\Firm\RecordOfManager,
    RecordPreparation\Firm\RecordOfPersonnel
};

class InviteeControllerTest extends ActivityTestCase
{
    protected $inviteeUri;
    protected $managerInvitee;
    protected $coordinatorInvitee;
    protected $consultantInvitee;
    protected $participantInvitee;
    protected $clientParticipant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->inviteeUri = $this->activityUri . "/{$this->managerActivity->id}/invitees";
        
        $manager = new RecordOfManager($this->firm, 0, "manager@email.org", "Password123");
        $this->connection->table("Manager")->insert($manager->toArrayForDbEntry());
        
        $activity = $this->managerActivity->activity;
        $program = $activity->program;
        
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
        
        $invitee = new RecordOfInvitee($activity, $this->activityParticipantOne_Manager, 0);
        $inviteeOne = new RecordOfInvitee($activity, $this->activityParticipant_coordinator,1);
        $inviteeTwo = new RecordOfInvitee($activity, $this->activityParticipantTwo_Consultant, 2);
        $inviteeThree = new RecordOfInvitee($activity, $this->activityParticipantThree_Participant, 3);
        $this->connection->table("Invitee")->insert($invitee->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($inviteeOne->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($inviteeTwo->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($inviteeThree->toArrayForDbEntry());
        
        $personnel = new RecordOfPersonnel($this->firm, 0);
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());
        
        $client = new RecordOfClient($this->firm, 0);
        $this->connection->table("Client")->insert($client->toArrayForDbEntry());
        
        $coordinator = new RecordOfCoordinator($program, $personnel, 0);
        $this->connection->table("Coordinator")->insert($coordinator->toArrayForDbEntry());
        
        $consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->connection->table("Consultant")->insert($consultant->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($program, 0);
        $this->connection->table("Participant")->insert($participant->toArrayForDbEntry());
        
        $this->clientParticipant = new RecordOfClientParticipant($client, $participant);
        $this->connection->table("ClientParticipant")->insert($this->clientParticipant->toArrayForDbEntry());
        
        $this->managerInvitee = new ManagerInvitee($manager, $invitee);
        $this->connection->table("ManagerInvitee")->insert($this->managerInvitee->toArrayForDbEntry());
        
        $this->coordinatorInvitee = new CoordinatorInvitee($coordinator, $inviteeOne);
        $this->connection->table("CoordinatorInvitee")->insert($this->coordinatorInvitee->toArrayForDbEntry());
        
        $this->consultantInvitee = new ConsultantInvitee($consultant, $inviteeTwo);
        $this->connection->table("ConsultantInvitee")->insert($this->consultantInvitee->toArrayForDbEntry());
        
        $this->participantInvitee = new ParticipantInvitee($participant, $inviteeThree);
        $this->connection->table("ParticipantInvitee")->insert($this->participantInvitee->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->managerInvitee->invitee->id,
            "willAttend" => $this->managerInvitee->invitee->willAttend,
            "attended" => $this->managerInvitee->invitee->attended,
            "manager" => [
                "id" => $this->managerInvitee->manager->id,
                "name" => $this->managerInvitee->manager->name,
            ],
            "coordinator" => null,
            "consultant" => null,
            "participant" => null,
        ];
        
        $uri = $this->inviteeUri . "/{$this->managerInvitee->invitee->id}";
        $this->get($uri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 4,
            "list" => [
                [
                    "id" => $this->managerInvitee->invitee->id,
                    "willAttend" => $this->managerInvitee->invitee->willAttend,
                    "attended" => $this->managerInvitee->invitee->attended,
                    "manager" => [
                        "id" => $this->managerInvitee->manager->id,
                        "name" => $this->managerInvitee->manager->name,
                    ],
                    "coordinator" => null,
                    "consultant" => null,
                    "participant" => null,
                ],
                [
                    "id" => $this->coordinatorInvitee->invitee->id,
                    "willAttend" => $this->coordinatorInvitee->invitee->willAttend,
                    "attended" => $this->coordinatorInvitee->invitee->attended,
                    "manager" => null,
                    "coordinator" => [
                        "id" => $this->coordinatorInvitee->coordinator->id,
                        "personnel" => [
                            "id" => $this->coordinatorInvitee->coordinator->personnel->id,
                            "name" => $this->coordinatorInvitee->coordinator->personnel->getFullName(),
                        ],
                    ],
                    "consultant" => null,
                    "participant" => null,
                ],
                [
                    "id" => $this->consultantInvitee->invitee->id,
                    "willAttend" => $this->consultantInvitee->invitee->willAttend,
                    "attended" => $this->consultantInvitee->invitee->attended,
                    "manager" => null,
                    "coordinator" => null,
                    "consultant" => [
                        "id" => $this->consultantInvitee->consultant->id,
                        "personnel" => [
                            "id" => $this->consultantInvitee->consultant->personnel->id,
                            "name" => $this->consultantInvitee->consultant->personnel->getFullName(),
                        ],
                    ],
                    "participant" => null,
                ],
                [
                    "id" => $this->participantInvitee->invitee->id,
                    "willAttend" => $this->participantInvitee->invitee->willAttend,
                    "attended" => $this->participantInvitee->invitee->attended,
                    "manager" => null,
                    "coordinator" => null,
                    "consultant" => null,
                    "participant" => [
                        "id" => $this->participantInvitee->participant->id,
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
        
        $this->get($this->inviteeUri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
 