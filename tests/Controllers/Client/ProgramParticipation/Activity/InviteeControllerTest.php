<?php

namespace Tests\Controllers\Client\ProgramParticipation\Activity;

use Tests\Controllers\ {
    Client\ProgramParticipation\ActivityTestCase,
    RecordPreparation\Firm\Client\RecordOfClientParticipant,
    RecordPreparation\Firm\Manager\RecordOfManagerInvitation,
    RecordPreparation\Firm\Program\Activity\RecordOfInvitation,
    RecordPreparation\Firm\Program\Consultant\RecordOfConsultantInvitation,
    RecordPreparation\Firm\Program\Coordinator\RecordOfCoordinatorInvitation,
    RecordPreparation\Firm\Program\Participant\RecordOfParticipantInvitation,
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
        $this->connection->table("Invitation")->truncate();
        $this->connection->table("ConsultantInvitation")->truncate();
        $this->connection->table("ManagerInvitation")->truncate();
        $this->connection->table("CoordinatorInvitation")->truncate();
        $this->connection->table("ParticipantInvitation")->truncate();
        
        $invitation = new RecordOfInvitation($activity, 0);
        $invitationOne = new RecordOfInvitation($activity, 1);
        $invitationTwo = new RecordOfInvitation($activity, 2);
        $invitationThree = new RecordOfInvitation($activity, 3);
        $this->connection->table("Invitation")->insert($invitation->toArrayForDbEntry());
        $this->connection->table("Invitation")->insert($invitationOne->toArrayForDbEntry());
        $this->connection->table("Invitation")->insert($invitationTwo->toArrayForDbEntry());
        $this->connection->table("Invitation")->insert($invitationThree->toArrayForDbEntry());
        
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
        
        $this->consultantInvitation = new RecordOfConsultantInvitation($consultant, $invitation);
        $this->connection->table("ConsultantInvitation")->insert($this->consultantInvitation->toArrayForDbEntry());
        
        $this->managerInvitation = new RecordOfManagerInvitation($manager, $invitationOne);
        $this->connection->table("ManagerInvitation")->insert($this->managerInvitation->toArrayForDbEntry());
        
        $this->coordinatorInvitation = new RecordOfCoordinatorInvitation($coordinator, $invitationTwo);
        $this->connection->table("CoordinatorInvitation")->insert($this->coordinatorInvitation->toArrayForDbEntry());
        
        $this->participantInvitation = new RecordOfParticipantInvitation($participant, $invitationThree);
        $this->connection->table("ParticipantInvitation")->insert($this->participantInvitation->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Invitation")->truncate();
        $this->connection->table("ConsultantInvitation")->truncate();
        $this->connection->table("ManagerInvitation")->truncate();
        $this->connection->table("CoordinatorInvitation")->truncate();
        $this->connection->table("ParticipantInvitation")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->consultantInvitation->invitation->id,
            "willAttend" => $this->consultantInvitation->invitation->willAttend,
            "attended" => $this->consultantInvitation->invitation->attended,
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
        
        $uri = $this->inviteeUri . "/{$this->consultantInvitation->invitation->id}";
        $this->get($uri, $this->programParticipation->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }

    public function test_showAll_200()
    {
        $response = [
            "total" => 4,
            "list" => [
                [
                    "id" => $this->consultantInvitation->invitation->id,
                    "willAttend" => $this->consultantInvitation->invitation->willAttend,
                    "attended" => $this->consultantInvitation->invitation->attended,
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
                    "id" => $this->managerInvitation->invitation->id,
                    "willAttend" => $this->managerInvitation->invitation->willAttend,
                    "attended" => $this->managerInvitation->invitation->attended,
                    "consultant" => null,
                    "manager" => [
                        "id" => $this->managerInvitation->manager->id,
                        "name" => $this->managerInvitation->manager->name,
                    ],
                    "coordinator" => null,
                    "participant" => null,
                ],
                [
                    "id" => $this->coordinatorInvitation->invitation->id,
                    "willAttend" => $this->coordinatorInvitation->invitation->willAttend,
                    "attended" => $this->coordinatorInvitation->invitation->attended,
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
                    "id" => $this->participantInvitation->invitation->id,
                    "willAttend" => $this->participantInvitation->invitation->willAttend,
                    "attended" => $this->participantInvitation->invitation->attended,
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
        
        $this->get($this->inviteeUri, $this->programParticipation->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
 