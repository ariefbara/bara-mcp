<?php

namespace Tests\Controllers\Client\AsTeamMember\ProgramParticipation;

use Tests\Controllers\ {
    Client\AsTeamMember\ProgramParticipationTestCase,
    RecordPreparation\Firm\Client\RecordOfClientParticipant,
    RecordPreparation\Firm\Manager\RecordOfManagerActivity,
    RecordPreparation\Firm\Program\Activity\RecordOfInvitation,
    RecordPreparation\Firm\Program\Consultant\RecordOfConsultantActivity,
    RecordPreparation\Firm\Program\Coordinator\RecordOfCoordinatorActivity,
    RecordPreparation\Firm\Program\Participant\RecordOfParticipantActivity,
    RecordPreparation\Firm\Program\Participant\RecordOfParticipantInvitation,
    RecordPreparation\Firm\Program\RecordOfActivity,
    RecordPreparation\Firm\Program\RecordOfActivityType,
    RecordPreparation\Firm\Program\RecordOfConsultant,
    RecordPreparation\Firm\Program\RecordOfCoordinator,
    RecordPreparation\Firm\Program\RecordOfParticipant,
    RecordPreparation\Firm\RecordOfClient,
    RecordPreparation\Firm\RecordOfManager,
    RecordPreparation\Firm\RecordOfPersonnel
};

class InvitationControllerTest extends ProgramParticipationTestCase
{

    protected $invitationUri;
    protected $participantActivity;
    protected $managerActivity;
    protected $coordinatorActivity;
    protected $consultantActivity;
    protected $clientParticipant;
    protected $participantInvitation_fromParticipant;
    protected $participantInvitationOne_fromManager;
    protected $participantInvitationTwo_fromCoordinator;
    protected $participantInvitationThree_fromConsultant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invitationUri = $this->programParticipationUri . "/{$this->programParticipation->id}/invitations";

        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("ParticipantActivity")->truncate();
        $this->connection->table("ManagerActivity")->truncate();
        $this->connection->table("CoordinatorActivity")->truncate();
        $this->connection->table("ConsultantActivity")->truncate();
        $this->connection->table("Invitation")->truncate();
        $this->connection->table("ParticipantInvitation")->truncate();

        $participant = $this->programParticipation->participant;
        $program = $participant->program;
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

        $participantOne = new RecordOfParticipant($program, 1);
        $this->connection->table("Participant")->insert($participantOne->toArrayForDbEntry());

        $coordinator = new RecordOfCoordinator($program, $personnel, 0);
        $this->connection->table("Coordinator")->insert($coordinator->toArrayForDbEntry());

        $consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->connection->table("Consultant")->insert($consultant->toArrayForDbEntry());

        $this->clientParticipant = new RecordOfClientParticipant($client, $participantOne);
        $this->connection->table("ClientParticipant")->insert($this->clientParticipant->toArrayForDbEntry());

        $this->participantActivity = new RecordOfParticipantActivity($participantOne, $activity);
        $this->connection->table("ParticipantActivity")->insert($this->participantActivity->toArrayForDbEntry());

        $this->managerActivity = new RecordOfManagerActivity($manager, $activityOne);
        $this->connection->table("ManagerActivity")->insert($this->managerActivity->toArrayForDbEntry());

        $this->coordinatorActivity = new RecordOfCoordinatorActivity($coordinator, $activityTwo);
        $this->connection->table("CoordinatorActivity")->insert($this->coordinatorActivity->toArrayForDbEntry());

        $this->consultantActivity = new RecordOfConsultantActivity($consultant, $activityThree);
        $this->connection->table("ConsultantActivity")->insert($this->consultantActivity->toArrayForDbEntry());


        $invitation = new RecordOfInvitation($activity, 0);
        $invitationOne = new RecordOfInvitation($activityOne, 1);
        $invitationTwo = new RecordOfInvitation($activityTwo, 2);
        $invitationThree = new RecordOfInvitation($activityThree, 3);
        $this->connection->table("Invitation")->insert($invitation->toArrayForDbEntry());
        $this->connection->table("Invitation")->insert($invitationOne->toArrayForDbEntry());
        $this->connection->table("Invitation")->insert($invitationTwo->toArrayForDbEntry());
        $this->connection->table("Invitation")->insert($invitationThree->toArrayForDbEntry());

        $this->participantInvitation_fromParticipant = new RecordOfParticipantInvitation($participant, $invitation);
        $this->participantInvitationOne_fromManager = new RecordOfParticipantInvitation($participant, $invitationOne);
        $this->participantInvitationTwo_fromCoordinator = new RecordOfParticipantInvitation($participant, $invitationTwo);
        $this->participantInvitationThree_fromConsultant = new RecordOfParticipantInvitation($participant, $invitationThree);
        $this->connection->table("ParticipantInvitation")->insert($this->participantInvitation_fromParticipant->toArrayForDbEntry());
        $this->connection->table("ParticipantInvitation")->insert($this->participantInvitationOne_fromManager->toArrayForDbEntry());
        $this->connection->table("ParticipantInvitation")->insert($this->participantInvitationTwo_fromCoordinator->toArrayForDbEntry());
        $this->connection->table("ParticipantInvitation")->insert($this->participantInvitationThree_fromConsultant->toArrayForDbEntry());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("ParticipantActivity")->truncate();
        $this->connection->table("ManagerActivity")->truncate();
        $this->connection->table("CoordinatorActivity")->truncate();
        $this->connection->table("ConsultantActivity")->truncate();
        $this->connection->table("Invitation")->truncate();
        $this->connection->table("ParticipantInvitation")->truncate();
    }

    public function test_show_200()
    {
        $response = [
            "id" => $this->participantInvitation_fromParticipant->id,
            "willAttend" => $this->participantInvitation_fromParticipant->invitation->willAttend,
            "attended" => $this->participantInvitation_fromParticipant->invitation->attended,
            "activity" => [
                "id" => $this->participantInvitation_fromParticipant->invitation->activity->id,
                "name" => $this->participantInvitation_fromParticipant->invitation->activity->name,
                "description" => $this->participantInvitation_fromParticipant->invitation->activity->description,
                "location" => $this->participantInvitation_fromParticipant->invitation->activity->location,
                "note" => $this->participantInvitation_fromParticipant->invitation->activity->note,
                "startTime" => $this->participantInvitation_fromParticipant->invitation->activity->startDateTime,
                "endTime" => $this->participantInvitation_fromParticipant->invitation->activity->endDateTime,
                "cancelled" => $this->participantInvitation_fromParticipant->invitation->activity->cancelled,
                "program" => [
                    "id" => $this->participantInvitation_fromParticipant->invitation->activity->program->id,
                    "name" => $this->participantInvitation_fromParticipant->invitation->activity->program->name,
                ],
                "activityType" => [
                    "id" => $this->participantInvitation_fromParticipant->invitation->activity->activityType->id,
                    "name" => $this->participantInvitation_fromParticipant->invitation->activity->activityType->name,
                ],
                "participant" => [
                    "id" => $this->participantActivity->participant->id,
                    "client" => [
                        "id" => $this->clientParticipant->client->id,
                        "name" => $this->clientParticipant->client->getFullName(),
                    ],
                    "user" => null,
                    "team" => null,
                ],
                "manager" => null,
                "coordinator" => null,
                "consultant" => null,
            ],
        ];

        $uri = $this->invitationUri . "/{$this->participantInvitation_fromParticipant->id}";
        $this->get($uri, $this->teamMember->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_200()
    {
        $response = [
            "total" => 4,
            "list" => [
                [
                    "id" => $this->participantInvitation_fromParticipant->id,
                    "willAttend" => $this->participantInvitation_fromParticipant->invitation->willAttend,
                    "attended" => $this->participantInvitation_fromParticipant->invitation->attended,
                    "activity" => [
                        "id" => $this->participantInvitation_fromParticipant->invitation->activity->id,
                        "name" => $this->participantInvitation_fromParticipant->invitation->activity->name,
                        "description" => $this->participantInvitation_fromParticipant->invitation->activity->description,
                        "location" => $this->participantInvitation_fromParticipant->invitation->activity->location,
                        "note" => $this->participantInvitation_fromParticipant->invitation->activity->note,
                        "startTime" => $this->participantInvitation_fromParticipant->invitation->activity->startDateTime,
                        "endTime" => $this->participantInvitation_fromParticipant->invitation->activity->endDateTime,
                        "cancelled" => $this->participantInvitation_fromParticipant->invitation->activity->cancelled,
                        "program" => [
                            "id" => $this->participantInvitation_fromParticipant->invitation->activity->program->id,
                            "name" => $this->participantInvitation_fromParticipant->invitation->activity->program->name,
                        ],
                        "activityType" => [
                            "id" => $this->participantInvitation_fromParticipant->invitation->activity->activityType->id,
                            "name" => $this->participantInvitation_fromParticipant->invitation->activity->activityType->name,
                        ],
                        "participant" => [
                            "id" => $this->participantActivity->participant->id,
                            "client" => [
                                "id" => $this->clientParticipant->client->id,
                                "name" => $this->clientParticipant->client->getFullName(),
                            ],
                            "user" => null,
                            "team" => null,
                        ],
                        "manager" => null,
                        "coordinator" => null,
                        "consultant" => null,
                    ],
                ],
                [
                    "id" => $this->participantInvitationOne_fromManager->id,
                    "willAttend" => $this->participantInvitationOne_fromManager->invitation->willAttend,
                    "attended" => $this->participantInvitationOne_fromManager->invitation->attended,
                    "activity" => [
                        "id" => $this->participantInvitationOne_fromManager->invitation->activity->id,
                        "name" => $this->participantInvitationOne_fromManager->invitation->activity->name,
                        "description" => $this->participantInvitationOne_fromManager->invitation->activity->description,
                        "location" => $this->participantInvitationOne_fromManager->invitation->activity->location,
                        "note" => $this->participantInvitationOne_fromManager->invitation->activity->note,
                        "startTime" => $this->participantInvitationOne_fromManager->invitation->activity->startDateTime,
                        "endTime" => $this->participantInvitationOne_fromManager->invitation->activity->endDateTime,
                        "cancelled" => $this->participantInvitationOne_fromManager->invitation->activity->cancelled,
                        "program" => [
                            "id" => $this->participantInvitationOne_fromManager->invitation->activity->program->id,
                            "name" => $this->participantInvitationOne_fromManager->invitation->activity->program->name,
                        ],
                        "activityType" => [
                            "id" => $this->participantInvitationOne_fromManager->invitation->activity->activityType->id,
                            "name" => $this->participantInvitationOne_fromManager->invitation->activity->activityType->name,
                        ],
                        "participant" => null,
                        "manager" => [
                            "id" => $this->managerActivity->manager->id,
                            "name" => $this->managerActivity->manager->name,
                        ],
                        "coordinator" => null,
                        "consultant" => null,
                    ],
                ],
                [
                    "id" => $this->participantInvitationTwo_fromCoordinator->id,
                    "willAttend" => $this->participantInvitationTwo_fromCoordinator->invitation->willAttend,
                    "attended" => $this->participantInvitationTwo_fromCoordinator->invitation->attended,
                    "activity" => [
                        "id" => $this->participantInvitationTwo_fromCoordinator->invitation->activity->id,
                        "name" => $this->participantInvitationTwo_fromCoordinator->invitation->activity->name,
                        "description" => $this->participantInvitationTwo_fromCoordinator->invitation->activity->description,
                        "location" => $this->participantInvitationTwo_fromCoordinator->invitation->activity->location,
                        "note" => $this->participantInvitationTwo_fromCoordinator->invitation->activity->note,
                        "startTime" => $this->participantInvitationTwo_fromCoordinator->invitation->activity->startDateTime,
                        "endTime" => $this->participantInvitationTwo_fromCoordinator->invitation->activity->endDateTime,
                        "cancelled" => $this->participantInvitationTwo_fromCoordinator->invitation->activity->cancelled,
                        "program" => [
                            "id" => $this->participantInvitationTwo_fromCoordinator->invitation->activity->program->id,
                            "name" => $this->participantInvitationTwo_fromCoordinator->invitation->activity->program->name,
                        ],
                        "activityType" => [
                            "id" => $this->participantInvitationTwo_fromCoordinator->invitation->activity->activityType->id,
                            "name" => $this->participantInvitationTwo_fromCoordinator->invitation->activity->activityType->name,
                        ],
                        "participant" => null,
                        "manager" => null,
                        "coordinator" => [
                            "id" => $this->coordinatorActivity->coordinator->id,
                            "personnel" => [
                                "id" => $this->coordinatorActivity->coordinator->personnel->id,
                                "name" => $this->coordinatorActivity->coordinator->personnel->getFullName(),
                            ],
                        ],
                        "consultant" => null,
                    ],
                ],
                [
                    "id" => $this->participantInvitationThree_fromConsultant->id,
                    "willAttend" => $this->participantInvitationThree_fromConsultant->invitation->willAttend,
                    "attended" => $this->participantInvitationThree_fromConsultant->invitation->attended,
                    "activity" => [
                        "id" => $this->participantInvitationThree_fromConsultant->invitation->activity->id,
                        "name" => $this->participantInvitationThree_fromConsultant->invitation->activity->name,
                        "description" => $this->participantInvitationThree_fromConsultant->invitation->activity->description,
                        "location" => $this->participantInvitationThree_fromConsultant->invitation->activity->location,
                        "note" => $this->participantInvitationThree_fromConsultant->invitation->activity->note,
                        "startTime" => $this->participantInvitationThree_fromConsultant->invitation->activity->startDateTime,
                        "endTime" => $this->participantInvitationThree_fromConsultant->invitation->activity->endDateTime,
                        "cancelled" => $this->participantInvitationThree_fromConsultant->invitation->activity->cancelled,
                        "program" => [
                            "id" => $this->participantInvitationThree_fromConsultant->invitation->activity->program->id,
                            "name" => $this->participantInvitationThree_fromConsultant->invitation->activity->program->name,
                        ],
                        "activityType" => [
                            "id" => $this->participantInvitationThree_fromConsultant->invitation->activity->activityType->id,
                            "name" => $this->participantInvitationThree_fromConsultant->invitation->activity->activityType->name,
                        ],
                        "participant" => null,
                        "manager" => null,
                        "coordinator" => null,
                        "consultant" => [
                            "id" => $this->consultantActivity->consultant->id,
                            "personnel" => [
                                "id" => $this->consultantActivity->consultant->personnel->id,
                                "name" => $this->consultantActivity->consultant->personnel->getFullName(),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->get($this->invitationUri, $this->teamMember->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }

}
