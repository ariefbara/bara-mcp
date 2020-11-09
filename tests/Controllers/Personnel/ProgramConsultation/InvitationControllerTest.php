<?php

namespace Tests\Controllers\Personnel\Coordinator;

use Tests\Controllers\RecordPreparation\Firm\ {
    Client\RecordOfClientParticipant,
    Manager\RecordOfManagerActivity,
    Program\Activity\RecordOfInvitation,
    Program\Consultant\RecordOfConsultantActivity,
    Program\Coordinator\RecordOfCoordinatorActivity,
    Program\Coordinator\RecordOfCoordinatorInvitation,
    Program\Participant\RecordOfParticipantActivity,
    Program\RecordOfActivity,
    Program\RecordOfActivityType,
    Program\RecordOfConsultant,
    Program\RecordOfCoordinator,
    Program\RecordOfParticipant,
    RecordOfClient,
    RecordOfManager,
    RecordOfPersonnel
};

class InvitationControllerTest extends CoordinatorTestCase
{
    protected $invitationUri;
    protected $coordinatorActivity;
    protected $managerActivity;
    protected $consultantActivity;
    protected $participantActivity;
    protected $clientParticipant;
    protected $coordinatorInvitation_fromCoordinator;
    protected $coordinatorInvitationOne_fromManager;
    protected $coordinatorInvitationTwo_fromConsultant;
    protected $coordinatorInvitationThree_fromParticipant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->invitationUri = $this->coordinatorUri . "/{$this->coordinator->id}/invitations";
        
        
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("CoordinatorActivity")->truncate();
        $this->connection->table("ManagerActivity")->truncate();
        $this->connection->table("ConsultantActivity")->truncate();
        $this->connection->table("ParticipantActivity")->truncate();
        $this->connection->table("Invitation")->truncate();
        $this->connection->table("CoordinatorInvitation")->truncate();
        
        $program = $this->coordinator->program;
        $firm = $program->firm;
        
        $activityType = new RecordOfActivityType($program, 0);
        $this->connection->table("ActivityType")->insert($activityType->toArrayForDbEntry());
        
        $activity = new RecordOfActivity($program, $activityType, 0);
        $activityOne = new RecordOfActivity($program, $activityType, 1);
        $activityTwo = new RecordOfActivity($program, $activityType, 2);
        $activityThree = new RecordOfActivity($program, $activityType, 3);
        $this->connection->table("Activity")->insert($activity->toArrayForDbEntry());
        $this->connection->table("Activity")->insert($activityOne->toArrayForDbEntry());
        $this->connection->table("Activity")->insert($activityTwo->toArrayForDbEntry());
        $this->connection->table("Activity")->insert($activityThree->toArrayForDbEntry());
        
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
        
        $this->coordinatorActivity = new RecordOfCoordinatorActivity($coordinator, $activity);
        $this->connection->table("CoordinatorActivity")->insert($this->coordinatorActivity->toArrayForDbEntry());
        
        $this->managerActivity = new RecordOfManagerActivity($manager, $activityOne);
        $this->connection->table("ManagerActivity")->insert($this->managerActivity->toArrayForDbEntry());
        
        $this->consultantActivity = new RecordOfConsultantActivity($consultant, $activityTwo);
        $this->connection->table("ConsultantActivity")->insert($this->consultantActivity->toArrayForDbEntry());
        
        $this->participantActivity = new RecordOfParticipantActivity($participant, $activityThree);
        $this->connection->table("ParticipantActivity")->insert($this->participantActivity->toArrayForDbEntry());
        
        
        $invitation = new RecordOfInvitation($activity, 0);
        $invitationOne = new RecordOfInvitation($activityOne, 1);
        $invitationTwo = new RecordOfInvitation($activityTwo, 2);
        $invitationThree = new RecordOfInvitation($activityThree, 3);
        $this->connection->table("Invitation")->insert($invitation->toArrayForDbEntry());
        $this->connection->table("Invitation")->insert($invitationOne->toArrayForDbEntry());
        $this->connection->table("Invitation")->insert($invitationTwo->toArrayForDbEntry());
        $this->connection->table("Invitation")->insert($invitationThree->toArrayForDbEntry());
        
        $this->coordinatorInvitation_fromCoordinator = new RecordOfCoordinatorInvitation($this->coordinator, $invitation);
        $this->coordinatorInvitationOne_fromManager = new RecordOfCoordinatorInvitation($this->coordinator, $invitationOne);
        $this->coordinatorInvitationTwo_fromConsultant = new RecordOfCoordinatorInvitation($this->coordinator, $invitationTwo);
        $this->coordinatorInvitationThree_fromParticipant = new RecordOfCoordinatorInvitation($this->coordinator, $invitationThree);
        $this->connection->table("CoordinatorInvitation")->insert($this->coordinatorInvitation_fromCoordinator->toArrayForDbEntry());
        $this->connection->table("CoordinatorInvitation")->insert($this->coordinatorInvitationOne_fromManager->toArrayForDbEntry());
        $this->connection->table("CoordinatorInvitation")->insert($this->coordinatorInvitationTwo_fromConsultant->toArrayForDbEntry());
        $this->connection->table("CoordinatorInvitation")->insert($this->coordinatorInvitationThree_fromParticipant->toArrayForDbEntry());
        
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("CoordinatorActivity")->truncate();
        $this->connection->table("ManagerActivity")->truncate();
        $this->connection->table("ConsultantActivity")->truncate();
        $this->connection->table("ParticipantActivity")->truncate();
        $this->connection->table("Invitation")->truncate();
        $this->connection->table("CoordinatorInvitation")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->coordinatorInvitation_fromCoordinator->id,
            "willAttend" => $this->coordinatorInvitation_fromCoordinator->invitation->willAttend,
            "attended" => $this->coordinatorInvitation_fromCoordinator->invitation->attended,
            "activity" => [
                "id" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->id,
                "name" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->name,
                "description" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->description,
                "location" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->location,
                "note" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->note,
                "startTime" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->startDateTime,
                "endTime" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->endDateTime,
                "cancelled" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->cancelled,
                "program" => [
                    "id" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->program->id,
                    "name" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->program->name,
                ],
                "activityType" => [
                    "id" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->activityType->id,
                    "name" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->activityType->name,
                ],
                "coordinator" => [
                    "id" => $this->coordinatorActivity->coordinator->id,
                    "personnel" => [
                        "id" => $this->coordinatorActivity->coordinator->personnel->id,
                        "name" => $this->coordinatorActivity->coordinator->personnel->getFullName(),
                    ],
                ],
                "manager" => null,
                "consultant" => null,
                "participant" => null,
            ],
        ];
        
        $uri = $this->invitationUri . "/{$this->coordinatorInvitation_fromCoordinator->id}";
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
                    "id" => $this->coordinatorInvitation_fromCoordinator->id,
                    "willAttend" => $this->coordinatorInvitation_fromCoordinator->invitation->willAttend,
                    "attended" => $this->coordinatorInvitation_fromCoordinator->invitation->attended,
                    "activity" => [
                        "id" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->id,
                        "name" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->name,
                        "description" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->description,
                        "location" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->location,
                        "note" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->note,
                        "startTime" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->startDateTime,
                        "endTime" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->endDateTime,
                        "cancelled" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->cancelled,
                        "program" => [
                            "id" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->program->id,
                            "name" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->program->name,
                        ],
                        "activityType" => [
                            "id" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->activityType->id,
                            "name" => $this->coordinatorInvitation_fromCoordinator->invitation->activity->activityType->name,
                        ],
                        "coordinator" => [
                            "id" => $this->coordinatorActivity->coordinator->id,
                            "personnel" => [
                                "id" => $this->coordinatorActivity->coordinator->personnel->id,
                                "name" => $this->coordinatorActivity->coordinator->personnel->getFullName(),
                            ],
                        ],
                        "manager" => null,
                        "consultant" => null,
                        "participant" => null,
                    ],
                ],
                [
                    "id" => $this->coordinatorInvitationOne_fromManager->id,
                    "willAttend" => $this->coordinatorInvitationOne_fromManager->invitation->willAttend,
                    "attended" => $this->coordinatorInvitationOne_fromManager->invitation->attended,
                    "activity" => [
                        "id" => $this->coordinatorInvitationOne_fromManager->invitation->activity->id,
                        "name" => $this->coordinatorInvitationOne_fromManager->invitation->activity->name,
                        "description" => $this->coordinatorInvitationOne_fromManager->invitation->activity->description,
                        "location" => $this->coordinatorInvitationOne_fromManager->invitation->activity->location,
                        "note" => $this->coordinatorInvitationOne_fromManager->invitation->activity->note,
                        "startTime" => $this->coordinatorInvitationOne_fromManager->invitation->activity->startDateTime,
                        "endTime" => $this->coordinatorInvitationOne_fromManager->invitation->activity->endDateTime,
                        "cancelled" => $this->coordinatorInvitationOne_fromManager->invitation->activity->cancelled,
                        "program" => [
                            "id" => $this->coordinatorInvitationOne_fromManager->invitation->activity->program->id,
                            "name" => $this->coordinatorInvitationOne_fromManager->invitation->activity->program->name,
                        ],
                        "activityType" => [
                            "id" => $this->coordinatorInvitationOne_fromManager->invitation->activity->activityType->id,
                            "name" => $this->coordinatorInvitationOne_fromManager->invitation->activity->activityType->name,
                        ],
                        "coordinator" => null,
                        "manager" => [
                            "id" => $this->managerActivity->manager->id,
                            "name" => $this->managerActivity->manager->name,
                        ],
                        "consultant" => null,
                        "participant" => null,
                    ],
                ],
                [
                    "id" => $this->coordinatorInvitationTwo_fromConsultant->id,
                    "willAttend" => $this->coordinatorInvitationTwo_fromConsultant->invitation->willAttend,
                    "attended" => $this->coordinatorInvitationTwo_fromConsultant->invitation->attended,
                    "activity" => [
                        "id" => $this->coordinatorInvitationTwo_fromConsultant->invitation->activity->id,
                        "name" => $this->coordinatorInvitationTwo_fromConsultant->invitation->activity->name,
                        "description" => $this->coordinatorInvitationTwo_fromConsultant->invitation->activity->description,
                        "location" => $this->coordinatorInvitationTwo_fromConsultant->invitation->activity->location,
                        "note" => $this->coordinatorInvitationTwo_fromConsultant->invitation->activity->note,
                        "startTime" => $this->coordinatorInvitationTwo_fromConsultant->invitation->activity->startDateTime,
                        "endTime" => $this->coordinatorInvitationTwo_fromConsultant->invitation->activity->endDateTime,
                        "cancelled" => $this->coordinatorInvitationTwo_fromConsultant->invitation->activity->cancelled,
                        "program" => [
                            "id" => $this->coordinatorInvitationTwo_fromConsultant->invitation->activity->program->id,
                            "name" => $this->coordinatorInvitationTwo_fromConsultant->invitation->activity->program->name,
                        ],
                        "activityType" => [
                            "id" => $this->coordinatorInvitationTwo_fromConsultant->invitation->activity->activityType->id,
                            "name" => $this->coordinatorInvitationTwo_fromConsultant->invitation->activity->activityType->name,
                        ],
                        "coordinator" => null,
                        "manager" => null,
                        "consultant" => [
                            "id" => $this->consultantActivity->consultant->id,
                            "personnel" => [
                                "id" => $this->consultantActivity->consultant->personnel->id,
                                "name" => $this->consultantActivity->consultant->personnel->getFullName(),
                            ],
                        ],
                        "participant" => null,
                    ],
                ],
                [
                    "id" => $this->coordinatorInvitationThree_fromParticipant->id,
                    "willAttend" => $this->coordinatorInvitationThree_fromParticipant->invitation->willAttend,
                    "attended" => $this->coordinatorInvitationThree_fromParticipant->invitation->attended,
                    "activity" => [
                        "id" => $this->coordinatorInvitationThree_fromParticipant->invitation->activity->id,
                        "name" => $this->coordinatorInvitationThree_fromParticipant->invitation->activity->name,
                        "description" => $this->coordinatorInvitationThree_fromParticipant->invitation->activity->description,
                        "location" => $this->coordinatorInvitationThree_fromParticipant->invitation->activity->location,
                        "note" => $this->coordinatorInvitationThree_fromParticipant->invitation->activity->note,
                        "startTime" => $this->coordinatorInvitationThree_fromParticipant->invitation->activity->startDateTime,
                        "endTime" => $this->coordinatorInvitationThree_fromParticipant->invitation->activity->endDateTime,
                        "cancelled" => $this->coordinatorInvitationThree_fromParticipant->invitation->activity->cancelled,
                        "program" => [
                            "id" => $this->coordinatorInvitationThree_fromParticipant->invitation->activity->program->id,
                            "name" => $this->coordinatorInvitationThree_fromParticipant->invitation->activity->program->name,
                        ],
                        "activityType" => [
                            "id" => $this->coordinatorInvitationThree_fromParticipant->invitation->activity->activityType->id,
                            "name" => $this->coordinatorInvitationThree_fromParticipant->invitation->activity->activityType->name,
                        ],
                        "coordinator" => null,
                        "manager" => null,
                        "consultant" => null,
                        "participant" => [
                            "id" => $this->participantActivity->participant->id,
                            "client" => [
                                "id" => $this->clientParticipant->client->id,
                                "name" => $this->clientParticipant->client->getFullName(),
                            ],
                            "user" => null,
                            "team" => null,
                        ],
                    ],
                ],
            ],
        ];
        
        $this->get($this->invitationUri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
