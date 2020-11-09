<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Firm\ {
    Client\RecordOfClientParticipant,
    Manager\RecordOfManagerActivity,
    Manager\RecordOfManagerInvitation,
    Program\Activity\RecordOfInvitation,
    Program\Consultant\RecordOfConsultantActivity,
    Program\Coordinator\RecordOfCoordinatorActivity,
    Program\Participant\RecordOfParticipantActivity,
    Program\RecordOfActivity,
    Program\RecordOfActivityType,
    Program\RecordOfConsultant,
    Program\RecordOfCoordinator,
    Program\RecordOfParticipant,
    RecordOfClient,
    RecordOfManager,
    RecordOfPersonnel,
    RecordOfProgram
};

class InvitationControllerTest extends ManagerTestCase
{
    protected $invitationUri;
    protected $managerActivity;
    protected $coordinatorActivity;
    protected $consultantActivity;
    protected $participantActivity;
    protected $clientParticipant;
    protected $managerInvitation_fromManager;
    protected $managerInvitationOne_fromCoordinator;
    protected $managerInvitationTwo_fromConsultant;
    protected $managerInvitationThree_fromParticipant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->invitationUri = $this->managerUri . "/invitations";
        
        $manager = new RecordOfManager($this->firm, 0, "manager@email.org", "Password123");
        $this->connection->table("Manager")->insert($manager->toArrayForDbEntry());
        
        $this->connection->table("Program")->truncate();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("ManagerActivity")->truncate();
        $this->connection->table("CoordinatorActivity")->truncate();
        $this->connection->table("ConsultantActivity")->truncate();
        $this->connection->table("ParticipantActivity")->truncate();
        $this->connection->table("Invitation")->truncate();
        $this->connection->table("ManagerInvitation")->truncate();
        
        $program = new RecordOfProgram($this->firm, 0);
        $this->connection->table("Program")->insert($program->toArrayForDbEntry());
        
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
        
        $this->managerActivity = new RecordOfManagerActivity($manager, $activity);
        $this->connection->table("ManagerActivity")->insert($this->managerActivity->toArrayForDbEntry());
        
        $this->coordinatorActivity = new RecordOfCoordinatorActivity($coordinator, $activityOne);
        $this->connection->table("CoordinatorActivity")->insert($this->coordinatorActivity->toArrayForDbEntry());
        
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
        
        $this->managerInvitation_fromManager = new RecordOfManagerInvitation($this->manager, $invitation);
        $this->managerInvitationOne_fromCoordinator = new RecordOfManagerInvitation($this->manager, $invitationOne);
        $this->managerInvitationTwo_fromConsultant = new RecordOfManagerInvitation($this->manager, $invitationTwo);
        $this->managerInvitationThree_fromParticipant = new RecordOfManagerInvitation($this->manager, $invitationThree);
        $this->connection->table("ManagerInvitation")->insert($this->managerInvitation_fromManager->toArrayForDbEntry());
        $this->connection->table("ManagerInvitation")->insert($this->managerInvitationOne_fromCoordinator->toArrayForDbEntry());
        $this->connection->table("ManagerInvitation")->insert($this->managerInvitationTwo_fromConsultant->toArrayForDbEntry());
        $this->connection->table("ManagerInvitation")->insert($this->managerInvitationThree_fromParticipant->toArrayForDbEntry());
        
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("ManagerActivity")->truncate();
        $this->connection->table("CoordinatorActivity")->truncate();
        $this->connection->table("ConsultantActivity")->truncate();
        $this->connection->table("ParticipantActivity")->truncate();
        $this->connection->table("Invitation")->truncate();
        $this->connection->table("ManagerInvitation")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->managerInvitation_fromManager->id,
            "willAttend" => $this->managerInvitation_fromManager->invitation->willAttend,
            "attended" => $this->managerInvitation_fromManager->invitation->attended,
            "activity" => [
                "id" => $this->managerInvitation_fromManager->invitation->activity->id,
                "name" => $this->managerInvitation_fromManager->invitation->activity->name,
                "description" => $this->managerInvitation_fromManager->invitation->activity->description,
                "location" => $this->managerInvitation_fromManager->invitation->activity->location,
                "note" => $this->managerInvitation_fromManager->invitation->activity->note,
                "startTime" => $this->managerInvitation_fromManager->invitation->activity->startDateTime,
                "endTime" => $this->managerInvitation_fromManager->invitation->activity->endDateTime,
                "cancelled" => $this->managerInvitation_fromManager->invitation->activity->cancelled,
                "program" => [
                    "id" => $this->managerInvitation_fromManager->invitation->activity->program->id,
                    "name" => $this->managerInvitation_fromManager->invitation->activity->program->name,
                ],
                "activityType" => [
                    "id" => $this->managerInvitation_fromManager->invitation->activity->activityType->id,
                    "name" => $this->managerInvitation_fromManager->invitation->activity->activityType->name,
                ],
                "manager" => [
                    "id" => $this->managerActivity->manager->id,
                    "name" => $this->managerActivity->manager->name,
                ],
                "coordinator" => null,
                "consultant" => null,
                "participant" => null,
            ],
        ];
        
        $uri = $this->invitationUri . "/{$this->managerInvitation_fromManager->id}";
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
                    "id" => $this->managerInvitation_fromManager->id,
                    "willAttend" => $this->managerInvitation_fromManager->invitation->willAttend,
                    "attended" => $this->managerInvitation_fromManager->invitation->attended,
                    "activity" => [
                        "id" => $this->managerInvitation_fromManager->invitation->activity->id,
                        "name" => $this->managerInvitation_fromManager->invitation->activity->name,
                        "description" => $this->managerInvitation_fromManager->invitation->activity->description,
                        "location" => $this->managerInvitation_fromManager->invitation->activity->location,
                        "note" => $this->managerInvitation_fromManager->invitation->activity->note,
                        "startTime" => $this->managerInvitation_fromManager->invitation->activity->startDateTime,
                        "endTime" => $this->managerInvitation_fromManager->invitation->activity->endDateTime,
                        "cancelled" => $this->managerInvitation_fromManager->invitation->activity->cancelled,
                        "program" => [
                            "id" => $this->managerInvitation_fromManager->invitation->activity->program->id,
                            "name" => $this->managerInvitation_fromManager->invitation->activity->program->name,
                        ],
                        "activityType" => [
                            "id" => $this->managerInvitation_fromManager->invitation->activity->activityType->id,
                            "name" => $this->managerInvitation_fromManager->invitation->activity->activityType->name,
                        ],
                        "manager" => [
                            "id" => $this->managerActivity->manager->id,
                            "name" => $this->managerActivity->manager->name,
                        ],
                        "coordinator" => null,
                        "consultant" => null,
                        "participant" => null,
                    ],
                ],
                [
                    "id" => $this->managerInvitationOne_fromCoordinator->id,
                    "willAttend" => $this->managerInvitationOne_fromCoordinator->invitation->willAttend,
                    "attended" => $this->managerInvitationOne_fromCoordinator->invitation->attended,
                    "activity" => [
                        "id" => $this->managerInvitationOne_fromCoordinator->invitation->activity->id,
                        "name" => $this->managerInvitationOne_fromCoordinator->invitation->activity->name,
                        "description" => $this->managerInvitationOne_fromCoordinator->invitation->activity->description,
                        "location" => $this->managerInvitationOne_fromCoordinator->invitation->activity->location,
                        "note" => $this->managerInvitationOne_fromCoordinator->invitation->activity->note,
                        "startTime" => $this->managerInvitationOne_fromCoordinator->invitation->activity->startDateTime,
                        "endTime" => $this->managerInvitationOne_fromCoordinator->invitation->activity->endDateTime,
                        "cancelled" => $this->managerInvitationOne_fromCoordinator->invitation->activity->cancelled,
                        "program" => [
                            "id" => $this->managerInvitationOne_fromCoordinator->invitation->activity->program->id,
                            "name" => $this->managerInvitationOne_fromCoordinator->invitation->activity->program->name,
                        ],
                        "activityType" => [
                            "id" => $this->managerInvitationOne_fromCoordinator->invitation->activity->activityType->id,
                            "name" => $this->managerInvitationOne_fromCoordinator->invitation->activity->activityType->name,
                        ],
                        "manager" => null,
                        "coordinator" => [
                            "id" => $this->coordinatorActivity->coordinator->id,
                            "personnel" => [
                                "id" => $this->coordinatorActivity->coordinator->personnel->id,
                                "name" => $this->coordinatorActivity->coordinator->personnel->getFullName(),
                            ],
                        ],
                        "consultant" => null,
                        "participant" => null,
                    ],
                ],
                [
                    "id" => $this->managerInvitationTwo_fromConsultant->id,
                    "willAttend" => $this->managerInvitationTwo_fromConsultant->invitation->willAttend,
                    "attended" => $this->managerInvitationTwo_fromConsultant->invitation->attended,
                    "activity" => [
                        "id" => $this->managerInvitationTwo_fromConsultant->invitation->activity->id,
                        "name" => $this->managerInvitationTwo_fromConsultant->invitation->activity->name,
                        "description" => $this->managerInvitationTwo_fromConsultant->invitation->activity->description,
                        "location" => $this->managerInvitationTwo_fromConsultant->invitation->activity->location,
                        "note" => $this->managerInvitationTwo_fromConsultant->invitation->activity->note,
                        "startTime" => $this->managerInvitationTwo_fromConsultant->invitation->activity->startDateTime,
                        "endTime" => $this->managerInvitationTwo_fromConsultant->invitation->activity->endDateTime,
                        "cancelled" => $this->managerInvitationTwo_fromConsultant->invitation->activity->cancelled,
                        "program" => [
                            "id" => $this->managerInvitationTwo_fromConsultant->invitation->activity->program->id,
                            "name" => $this->managerInvitationTwo_fromConsultant->invitation->activity->program->name,
                        ],
                        "activityType" => [
                            "id" => $this->managerInvitationTwo_fromConsultant->invitation->activity->activityType->id,
                            "name" => $this->managerInvitationTwo_fromConsultant->invitation->activity->activityType->name,
                        ],
                        "manager" => null,
                        "coordinator" => null,
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
                    "id" => $this->managerInvitationThree_fromParticipant->id,
                    "willAttend" => $this->managerInvitationThree_fromParticipant->invitation->willAttend,
                    "attended" => $this->managerInvitationThree_fromParticipant->invitation->attended,
                    "activity" => [
                        "id" => $this->managerInvitationThree_fromParticipant->invitation->activity->id,
                        "name" => $this->managerInvitationThree_fromParticipant->invitation->activity->name,
                        "description" => $this->managerInvitationThree_fromParticipant->invitation->activity->description,
                        "location" => $this->managerInvitationThree_fromParticipant->invitation->activity->location,
                        "note" => $this->managerInvitationThree_fromParticipant->invitation->activity->note,
                        "startTime" => $this->managerInvitationThree_fromParticipant->invitation->activity->startDateTime,
                        "endTime" => $this->managerInvitationThree_fromParticipant->invitation->activity->endDateTime,
                        "cancelled" => $this->managerInvitationThree_fromParticipant->invitation->activity->cancelled,
                        "program" => [
                            "id" => $this->managerInvitationThree_fromParticipant->invitation->activity->program->id,
                            "name" => $this->managerInvitationThree_fromParticipant->invitation->activity->program->name,
                        ],
                        "activityType" => [
                            "id" => $this->managerInvitationThree_fromParticipant->invitation->activity->activityType->id,
                            "name" => $this->managerInvitationThree_fromParticipant->invitation->activity->activityType->name,
                        ],
                        "manager" => null,
                        "coordinator" => null,
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
        
        $this->get($this->invitationUri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
