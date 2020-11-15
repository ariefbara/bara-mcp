<?php

namespace Tests\Controllers\Personnel\Coordinator\Activity;

use Tests\Controllers\ {
    Personnel\Coordinator\ActivityTestCase,
    RecordPreparation\Firm\Client\RecordOfClientParticipant,
    RecordPreparation\Firm\Manager\RecordOfActivityInvitation as RecordOfActivityInvitation2,
    RecordPreparation\Firm\Program\Activity\RecordOfInvitee,
    RecordPreparation\Firm\Program\Consultant\RecordOfActivityInvitation as RecordOfActivityInvitation3,
    RecordPreparation\Firm\Program\Coordinator\RecordOfActivityInvitation,
    RecordPreparation\Firm\Program\Participant\RecordOfActivityInvitation as RecordOfActivityInvitation4,
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
    protected $coordinatorInvitation;
    protected $managerInvitation;
    protected $consultantInvitation;
    protected $participantInvitation;
    protected $clientParticipant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->inviteeUri = $this->activityUri . "/{$this->coordinatorActivity->id}/invitees";
        
        
        $activity = $this->coordinatorActivity->activity;
        $program = $activity->program;
        $firm = $program->firm;
        
        $this->connection->table("Client")->truncate();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
        
        $invitation = new RecordOfInvitee($activity, $this->activityParticipant_coordinator,0);
        $invitationOne = new RecordOfInvitee($activity, $this->activityParticipant_coordinator, 1);
        $invitationTwo = new RecordOfInvitee($activity, $this->activityParticipant_coordinator, 2);
        $invitationThree = new RecordOfInvitee($activity, $this->activityParticipant_coordinator, 3);
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
        
        $coordinator = new RecordOfCoordinator($program, $personnel, 0);
        $this->connection->table("Coordinator")->insert($coordinator->toArrayForDbEntry());
        
        $consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->connection->table("Consultant")->insert($consultant->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($program, 0);
        $this->connection->table("Participant")->insert($participant->toArrayForDbEntry());
        
        $this->clientParticipant = new RecordOfClientParticipant($client, $participant);
        $this->connection->table("ClientParticipant")->insert($this->clientParticipant->toArrayForDbEntry());
        
        $this->coordinatorInvitation = new RecordOfActivityInvitation($coordinator, $invitation);
        $this->connection->table("CoordinatorInvitee")->insert($this->coordinatorInvitation->toArrayForDbEntry());
        
        $this->managerInvitation = new RecordOfActivityInvitation2($manager, $invitationOne);
        $this->connection->table("ManagerInvitee")->insert($this->managerInvitation->toArrayForDbEntry());
        
        $this->consultantInvitation = new RecordOfActivityInvitation3($consultant, $invitationTwo);
        $this->connection->table("ConsultantInvitee")->insert($this->consultantInvitation->toArrayForDbEntry());
        
        $this->participantInvitation = new RecordOfActivityInvitation4($participant, $invitationThree);
        $this->connection->table("ParticipantInvitee")->insert($this->participantInvitation->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        
        $this->connection->table("Client")->truncate();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->coordinatorInvitation->invitee->id,
            "willAttend" => $this->coordinatorInvitation->invitee->willAttend,
            "attended" => $this->coordinatorInvitation->invitee->attended,
            "coordinator" => [
                "id" => $this->coordinatorInvitation->coordinator->id,
                "personnel" => [
                    "id" => $this->coordinatorInvitation->coordinator->personnel->id,
                    "name" => $this->coordinatorInvitation->coordinator->personnel->getFullName(),
                ],
            ],
            "manager" => null,
            "consultant" => null,
            "participant" => null,
        ];
        
        $uri = $this->inviteeUri . "/{$this->coordinatorInvitation->invitee->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }

    public function test_showAll_200()
    {
        $response = [
            "total" => 4,
            "list" => [
                [
                    "id" => $this->coordinatorInvitation->invitee->id,
                    "willAttend" => $this->coordinatorInvitation->invitee->willAttend,
                    "attended" => $this->coordinatorInvitation->invitee->attended,
                    "coordinator" => [
                        "id" => $this->coordinatorInvitation->coordinator->id,
                        "personnel" => [
                            "id" => $this->coordinatorInvitation->coordinator->personnel->id,
                            "name" => $this->coordinatorInvitation->coordinator->personnel->getFullName(),
                        ],
                    ],
                    "manager" => null,
                    "consultant" => null,
                    "participant" => null,
                ],
                [
                    "id" => $this->managerInvitation->invitee->id,
                    "willAttend" => $this->managerInvitation->invitee->willAttend,
                    "attended" => $this->managerInvitation->invitee->attended,
                    "coordinator" => null,
                    "manager" => [
                        "id" => $this->managerInvitation->manager->id,
                        "name" => $this->managerInvitation->manager->name,
                    ],
                    "consultant" => null,
                    "participant" => null,
                ],
                [
                    "id" => $this->consultantInvitation->invitee->id,
                    "willAttend" => $this->consultantInvitation->invitee->willAttend,
                    "attended" => $this->consultantInvitation->invitee->attended,
                    "coordinator" => null,
                    "manager" => null,
                    "consultant" => [
                        "id" => $this->consultantInvitation->consultant->id,
                        "personnel" => [
                            "id" => $this->consultantInvitation->consultant->personnel->id,
                            "name" => $this->consultantInvitation->consultant->personnel->getFullName(),
                        ],
                    ],
                    "participant" => null,
                ],
                [
                    "id" => $this->participantInvitation->invitee->id,
                    "willAttend" => $this->participantInvitation->invitee->willAttend,
                    "attended" => $this->participantInvitation->invitee->attended,
                    "coordinator" => null,
                    "manager" => null,
                    "consultant" => null,
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
        
        $this->get($this->inviteeUri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
 