<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use Tests\Controllers\RecordPreparation\Firm\{
    Client\RecordOfClientParticipant,
    Manager\RecordOfManagerActivity,
    Program\Activity\RecordOfInvitation,
    Program\Consultant\RecordOfConsultantActivity,
    Program\Consultant\RecordOfConsultantInvitation,
    Program\Coordinator\RecordOfCoordinatorActivity,
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

class InvitationControllerTest extends ProgramConsultationTestCase
{

    protected $invitationUri;
    protected $consultantActivity;
    protected $managerActivity;
    protected $coordinatorActivity;
    protected $participantActivity;
    protected $clientParticipant;
    protected $consultantInvitation_fromConsultant;
    protected $consultantInvitationOne_fromManager;
    protected $consultantInvitationTwo_fromCoordinator;
    protected $consultantInvitationThree_fromParticipant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invitationUri = $this->programConsultationUri . "/invitations";

        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("ConsultantActivity")->truncate();
        $this->connection->table("ManagerActivity")->truncate();
        $this->connection->table("CoordinatorActivity")->truncate();
        $this->connection->table("ParticipantActivity")->truncate();
        $this->connection->table("Invitation")->truncate();
        $this->connection->table("ConsultantInvitation")->truncate();

        $program = $this->programConsultation->program;
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

        $consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->connection->table("Consultant")->insert($consultant->toArrayForDbEntry());

        $coordinator = new RecordOfCoordinator($program, $personnel, 0);
        $this->connection->table("Coordinator")->insert($coordinator->toArrayForDbEntry());

        $participant = new RecordOfParticipant($program, 0);
        $this->connection->table("Participant")->insert($participant->toArrayForDbEntry());

        $this->clientParticipant = new RecordOfClientParticipant($client, $participant);
        $this->connection->table("ClientParticipant")->insert($this->clientParticipant->toArrayForDbEntry());

        $this->consultantActivity = new RecordOfConsultantActivity($consultant, $activity);
        $this->connection->table("ConsultantActivity")->insert($this->consultantActivity->toArrayForDbEntry());

        $this->managerActivity = new RecordOfManagerActivity($manager, $activityOne);
        $this->connection->table("ManagerActivity")->insert($this->managerActivity->toArrayForDbEntry());

        $this->coordinatorActivity = new RecordOfCoordinatorActivity($coordinator, $activityTwo);
        $this->connection->table("CoordinatorActivity")->insert($this->coordinatorActivity->toArrayForDbEntry());

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

        $this->consultantInvitation_fromConsultant = new RecordOfConsultantInvitation($this->programConsultation, $invitation);
        $this->consultantInvitationOne_fromManager = new RecordOfConsultantInvitation($this->programConsultation, $invitationOne);
        $this->consultantInvitationTwo_fromCoordinator = new RecordOfConsultantInvitation($this->programConsultation,
                $invitationTwo);
        $this->consultantInvitationThree_fromParticipant = new RecordOfConsultantInvitation($this->programConsultation,
                $invitationThree);
        $this->connection->table("ConsultantInvitation")->insert($this->consultantInvitation_fromConsultant->toArrayForDbEntry());
        $this->connection->table("ConsultantInvitation")->insert($this->consultantInvitationOne_fromManager->toArrayForDbEntry());
        $this->connection->table("ConsultantInvitation")->insert($this->consultantInvitationTwo_fromCoordinator->toArrayForDbEntry());
        $this->connection->table("ConsultantInvitation")->insert($this->consultantInvitationThree_fromParticipant->toArrayForDbEntry());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("ConsultantActivity")->truncate();
        $this->connection->table("ManagerActivity")->truncate();
        $this->connection->table("CoordinatorActivity")->truncate();
        $this->connection->table("ParticipantActivity")->truncate();
        $this->connection->table("Invitation")->truncate();
        $this->connection->table("ConsultantInvitation")->truncate();
    }

    public function test_show_200()
    {
        $response = [
            "id" => $this->consultantInvitation_fromConsultant->id,
            "willAttend" => $this->consultantInvitation_fromConsultant->invitation->willAttend,
            "attended" => $this->consultantInvitation_fromConsultant->invitation->attended,
            "activity" => [
                "id" => $this->consultantInvitation_fromConsultant->invitation->activity->id,
                "name" => $this->consultantInvitation_fromConsultant->invitation->activity->name,
                "description" => $this->consultantInvitation_fromConsultant->invitation->activity->description,
                "location" => $this->consultantInvitation_fromConsultant->invitation->activity->location,
                "note" => $this->consultantInvitation_fromConsultant->invitation->activity->note,
                "startTime" => $this->consultantInvitation_fromConsultant->invitation->activity->startDateTime,
                "endTime" => $this->consultantInvitation_fromConsultant->invitation->activity->endDateTime,
                "cancelled" => $this->consultantInvitation_fromConsultant->invitation->activity->cancelled,
                "program" => [
                    "id" => $this->consultantInvitation_fromConsultant->invitation->activity->program->id,
                    "name" => $this->consultantInvitation_fromConsultant->invitation->activity->program->name,
                ],
                "activityType" => [
                    "id" => $this->consultantInvitation_fromConsultant->invitation->activity->activityType->id,
                    "name" => $this->consultantInvitation_fromConsultant->invitation->activity->activityType->name,
                ],
                "consultant" => [
                    "id" => $this->consultantActivity->consultant->id,
                    "personnel" => [
                        "id" => $this->consultantActivity->consultant->personnel->id,
                        "name" => $this->consultantActivity->consultant->personnel->getFullName(),
                    ],
                ],
                "manager" => null,
                "coordinator" => null,
                "participant" => null,
            ],
        ];

        $uri = $this->invitationUri . "/{$this->consultantInvitation_fromConsultant->id}";
        $this->get($uri, $this->programConsultation->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }

    public function test_showAll_200()
    {
        $response = [
            "total" => 4,
            "list" => [
                [
                    "id" => $this->consultantInvitation_fromConsultant->id,
                    "willAttend" => $this->consultantInvitation_fromConsultant->invitation->willAttend,
                    "attended" => $this->consultantInvitation_fromConsultant->invitation->attended,
                    "activity" => [
                        "id" => $this->consultantInvitation_fromConsultant->invitation->activity->id,
                        "name" => $this->consultantInvitation_fromConsultant->invitation->activity->name,
                        "description" => $this->consultantInvitation_fromConsultant->invitation->activity->description,
                        "location" => $this->consultantInvitation_fromConsultant->invitation->activity->location,
                        "note" => $this->consultantInvitation_fromConsultant->invitation->activity->note,
                        "startTime" => $this->consultantInvitation_fromConsultant->invitation->activity->startDateTime,
                        "endTime" => $this->consultantInvitation_fromConsultant->invitation->activity->endDateTime,
                        "cancelled" => $this->consultantInvitation_fromConsultant->invitation->activity->cancelled,
                        "program" => [
                            "id" => $this->consultantInvitation_fromConsultant->invitation->activity->program->id,
                            "name" => $this->consultantInvitation_fromConsultant->invitation->activity->program->name,
                        ],
                        "activityType" => [
                            "id" => $this->consultantInvitation_fromConsultant->invitation->activity->activityType->id,
                            "name" => $this->consultantInvitation_fromConsultant->invitation->activity->activityType->name,
                        ],
                        "consultant" => [
                            "id" => $this->consultantActivity->consultant->id,
                            "personnel" => [
                                "id" => $this->consultantActivity->consultant->personnel->id,
                                "name" => $this->consultantActivity->consultant->personnel->getFullName(),
                            ],
                        ],
                        "manager" => null,
                        "coordinator" => null,
                        "participant" => null,
                    ],
                ],
                [
                    "id" => $this->consultantInvitationOne_fromManager->id,
                    "willAttend" => $this->consultantInvitationOne_fromManager->invitation->willAttend,
                    "attended" => $this->consultantInvitationOne_fromManager->invitation->attended,
                    "activity" => [
                        "id" => $this->consultantInvitationOne_fromManager->invitation->activity->id,
                        "name" => $this->consultantInvitationOne_fromManager->invitation->activity->name,
                        "description" => $this->consultantInvitationOne_fromManager->invitation->activity->description,
                        "location" => $this->consultantInvitationOne_fromManager->invitation->activity->location,
                        "note" => $this->consultantInvitationOne_fromManager->invitation->activity->note,
                        "startTime" => $this->consultantInvitationOne_fromManager->invitation->activity->startDateTime,
                        "endTime" => $this->consultantInvitationOne_fromManager->invitation->activity->endDateTime,
                        "cancelled" => $this->consultantInvitationOne_fromManager->invitation->activity->cancelled,
                        "program" => [
                            "id" => $this->consultantInvitationOne_fromManager->invitation->activity->program->id,
                            "name" => $this->consultantInvitationOne_fromManager->invitation->activity->program->name,
                        ],
                        "activityType" => [
                            "id" => $this->consultantInvitationOne_fromManager->invitation->activity->activityType->id,
                            "name" => $this->consultantInvitationOne_fromManager->invitation->activity->activityType->name,
                        ],
                        "consultant" => null,
                        "manager" => [
                            "id" => $this->managerActivity->manager->id,
                            "name" => $this->managerActivity->manager->name,
                        ],
                        "coordinator" => null,
                        "participant" => null,
                    ],
                ],
                [
                    "id" => $this->consultantInvitationTwo_fromCoordinator->id,
                    "willAttend" => $this->consultantInvitationTwo_fromCoordinator->invitation->willAttend,
                    "attended" => $this->consultantInvitationTwo_fromCoordinator->invitation->attended,
                    "activity" => [
                        "id" => $this->consultantInvitationTwo_fromCoordinator->invitation->activity->id,
                        "name" => $this->consultantInvitationTwo_fromCoordinator->invitation->activity->name,
                        "description" => $this->consultantInvitationTwo_fromCoordinator->invitation->activity->description,
                        "location" => $this->consultantInvitationTwo_fromCoordinator->invitation->activity->location,
                        "note" => $this->consultantInvitationTwo_fromCoordinator->invitation->activity->note,
                        "startTime" => $this->consultantInvitationTwo_fromCoordinator->invitation->activity->startDateTime,
                        "endTime" => $this->consultantInvitationTwo_fromCoordinator->invitation->activity->endDateTime,
                        "cancelled" => $this->consultantInvitationTwo_fromCoordinator->invitation->activity->cancelled,
                        "program" => [
                            "id" => $this->consultantInvitationTwo_fromCoordinator->invitation->activity->program->id,
                            "name" => $this->consultantInvitationTwo_fromCoordinator->invitation->activity->program->name,
                        ],
                        "activityType" => [
                            "id" => $this->consultantInvitationTwo_fromCoordinator->invitation->activity->activityType->id,
                            "name" => $this->consultantInvitationTwo_fromCoordinator->invitation->activity->activityType->name,
                        ],
                        "consultant" => null,
                        "manager" => null,
                        "coordinator" => [
                            "id" => $this->coordinatorActivity->coordinator->id,
                            "personnel" => [
                                "id" => $this->coordinatorActivity->coordinator->personnel->id,
                                "name" => $this->coordinatorActivity->coordinator->personnel->getFullName(),
                            ],
                        ],
                        "participant" => null,
                    ],
                ],
                [
                    "id" => $this->consultantInvitationThree_fromParticipant->id,
                    "willAttend" => $this->consultantInvitationThree_fromParticipant->invitation->willAttend,
                    "attended" => $this->consultantInvitationThree_fromParticipant->invitation->attended,
                    "activity" => [
                        "id" => $this->consultantInvitationThree_fromParticipant->invitation->activity->id,
                        "name" => $this->consultantInvitationThree_fromParticipant->invitation->activity->name,
                        "description" => $this->consultantInvitationThree_fromParticipant->invitation->activity->description,
                        "location" => $this->consultantInvitationThree_fromParticipant->invitation->activity->location,
                        "note" => $this->consultantInvitationThree_fromParticipant->invitation->activity->note,
                        "startTime" => $this->consultantInvitationThree_fromParticipant->invitation->activity->startDateTime,
                        "endTime" => $this->consultantInvitationThree_fromParticipant->invitation->activity->endDateTime,
                        "cancelled" => $this->consultantInvitationThree_fromParticipant->invitation->activity->cancelled,
                        "program" => [
                            "id" => $this->consultantInvitationThree_fromParticipant->invitation->activity->program->id,
                            "name" => $this->consultantInvitationThree_fromParticipant->invitation->activity->program->name,
                        ],
                        "activityType" => [
                            "id" => $this->consultantInvitationThree_fromParticipant->invitation->activity->activityType->id,
                            "name" => $this->consultantInvitationThree_fromParticipant->invitation->activity->activityType->name,
                        ],
                        "consultant" => null,
                        "manager" => null,
                        "coordinator" => null,
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

        $this->get($this->invitationUri, $this->programConsultation->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }

}
