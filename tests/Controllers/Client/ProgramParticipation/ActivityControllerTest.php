<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Firm\ {
    Program\Activity\RecordOfInvitee,
    Program\Coordinator\RecordOfActivityInvitation as RecordOfActivityInvitation2,
    Program\Participant\RecordOfActivityInvitation,
    Program\Participant\RecordOfParticipantActivity,
    Program\RecordOfActivity,
    Program\RecordOfConsultant,
    Program\RecordOfCoordinator,
    Program\RecordOfParticipant,
    RecordOfManager,
    RecordOfPersonnel
};

class ActivityControllerTest extends ActivityTestCase
{

    protected $participantActivityOne;
    protected $participantOne;
    protected $manager;
    protected $coordinatorOne;
    protected $coordinatorTwo;
    protected $consultant;
    protected $invitation;
    protected $invitationOne_coordinatorOne;
    protected $requestInput;
    protected $updateInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();

        $program = $this->programParticipation->participant->program;
        $firm = $program->firm;
        $activityType = $this->participantActivity->activity->activityType;

        $personnel = new RecordOfPersonnel($firm, 0);
        $personnelOne = new RecordOfPersonnel($firm, 1);
        $personnelTwo = new RecordOfPersonnel($firm, 2);
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());
        $this->connection->table("Personnel")->insert($personnelOne->toArrayForDbEntry());
        $this->connection->table("Personnel")->insert($personnelTwo->toArrayForDbEntry());
        
        $activity = new RecordOfActivity($program, $activityType, 1);
        $this->connection->table("Activity")->insert($activity->toArrayForDbEntry());

        $this->participantActivityOne = new RecordOfParticipantActivity($this->programParticipation->participant, $activity);
        $this->connection->table("ParticipantActivity")->insert($this->participantActivityOne->toArrayForDbEntry());

        $this->participantOne = new RecordOfParticipant($program, 1);
        $this->connection->table("Participant")->insert($this->participantOne->toArrayForDbEntry());

        $this->manager = new RecordOfManager($firm, 0, "manager@email.org", "Passwrod123");
        $this->connection->table("Manager")->insert($this->manager->toArrayForDbEntry());

        $this->coordinatorOne = new RecordOfCoordinator($program, $personnelOne, 1);
        $this->coordinatorTwo = new RecordOfCoordinator($program, $personnelTwo, 2);
        $this->connection->table("Coordinator")->insert($this->coordinatorOne->toArrayForDbEntry());
        $this->connection->table("Coordinator")->insert($this->coordinatorTwo->toArrayForDbEntry());

        $this->consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->connection->table("Consultant")->insert($this->consultant->toArrayForDbEntry());

        $this->invitation = new RecordOfInvitee($activity, $this->activityParticipantThree_Participant, 0);
        $this->invitationOne_coordinatorOne = new RecordOfInvitee($activity, $this->activityParticipantThree_Participant, 1);
        $this->connection->table("Invitee")->insert($this->invitation->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($this->invitationOne_coordinatorOne->toArrayForDbEntry());

        $participantInvitation = new RecordOfActivityInvitation($this->participantOne, $this->invitation);
        $this->connection->table("ParticipantInvitee")->insert($participantInvitation->toArrayForDbEntry());

        $coordinatorInvitation = new RecordOfActivityInvitation2($this->coordinatorOne,
                $this->invitationOne_coordinatorOne);
        $this->connection->table("CoordinatorInvitee")->insert($coordinatorInvitation->toArrayForDbEntry());

        $this->updateInput = [
            "name" => "new activity name",
            "description" => "new activity description",
            "location" => "new activity location",
            "note" => "new activity note",
            "startTime" => (new DateTimeImmutable("+48 hours"))->format("Y-m-d H:i:s"),
            "endTime" => (new DateTimeImmutable("+52 hours"))->format("Y-m-d H:i:s"),
            "invitedParticipantList" => [
                $this->participantOne->id,
            ],
            "invitedManagerList" => [
                $this->manager->id,
            ],
            "invitedCoordinatorList" => [
                $this->coordinatorTwo->id,
            ],
            "invitedConsultantList" => [
                $this->consultant->id,
            ],
        ];
        $this->requestInput = $this->updateInput;
        $this->requestInput["programId"] = $program->id;
        $this->requestInput["activityTypeId"] = $activityType->id;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
    }

    public function test_initiate_201()
    {
        $this->connection->table("ParticipantActivity")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();

        $response = [
            "activityType" => [
                "id" => $this->participantActivity->activity->activityType->id,
                "name" => $this->participantActivity->activity->activityType->name,
            ],
            "name" => $this->requestInput["name"],
            "description" => $this->requestInput["description"],
            "startTime" => $this->requestInput["startTime"],
            "endTime" => $this->requestInput["endTime"],
            "location" => $this->requestInput["location"],
            "note" => $this->requestInput["note"],
            "cancelled" => false,
        ];

        $this->post($this->activityUri, $this->requestInput, $this->programParticipation->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(201);

        $activityEntry = [
            "Program_id" => $this->requestInput["programId"],
            "ActivityType_id" => $this->requestInput["activityTypeId"],
            "name" => $this->requestInput["name"],
            "description" => $this->requestInput["description"],
            "location" => $this->requestInput["location"],
            "note" => $this->requestInput["note"],
            "startDateTime" => $this->requestInput["startTime"],
            "endDateTime" => $this->requestInput["endTime"],
        ];
        $this->seeInDatabase("Activity", $activityEntry);

        $participantActivityEntry = [
            "Participant_id" => $this->programParticipation->id,
        ];
        $this->seeInDatabase("ParticipantActivity", $participantActivityEntry);

        $invitationEntry = [
            "invitationCancelled" => false,
            "willAttend" => null,
            "attended" => null,
        ];
        $this->seeInDatabase("Invitee", $invitationEntry);

        $participantInvitationEntry = [
            "Participant_id" => $this->participantOne->id,
        ];
        $this->seeInDatabase("ParticipantInvitee", $participantInvitationEntry);

        $managerInvitationEntry = [
            "Manager_id" => $this->manager->id,
        ];
        $this->seeInDatabase("ManagerInvitee", $managerInvitationEntry);

        $coordinatorInvitationEntry = [
            "Coordinator_id" => $this->coordinatorTwo->id,
        ];
        $this->seeInDatabase("CoordinatorInvitee", $coordinatorInvitationEntry);

        $consultantInvitationEntry = [
            "Consultant_id" => $this->consultant->id,
        ];
        $this->seeInDatabase("ConsultantInvitee", $consultantInvitationEntry);
    }

    public function test_update_200()
    {
        $response = [
            "id" => $this->participantActivityOne->id,
            "name" => $this->updateInput["name"],
            "description" => $this->updateInput["description"],
            "startTime" => $this->updateInput["startTime"],
            "endTime" => $this->updateInput["endTime"],
            "location" => $this->updateInput["location"],
            "note" => $this->updateInput["note"],
            "cancelled" => false,
        ];

        $uri = $this->activityUri . "/{$this->participantActivityOne->id}";
        $this->patch($uri, $this->updateInput, $this->programParticipation->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);

        $activityEntry = [
            "id" => $this->participantActivityOne->activity->id,
            "name" => $this->updateInput["name"],
            "description" => $this->updateInput["description"],
            "location" => $this->updateInput["location"],
            "note" => $this->updateInput["note"],
            "startDateTime" => $this->updateInput["startTime"],
            "endDateTime" => $this->updateInput["endTime"],
        ];
        $this->seeInDatabase("Activity", $activityEntry);

        $managerInvitationEntry = [
            "Manager_id" => $this->manager->id,
        ];
        $this->seeInDatabase("ManagerInvitee", $managerInvitationEntry);

        $coordinatorInvitationEntry = [
            "Coordinator_id" => $this->coordinatorTwo->id,
        ];
        $this->seeInDatabase("CoordinatorInvitee", $coordinatorInvitationEntry);

        $consultantInvitationEntry = [
            "Consultant_id" => $this->consultant->id,
        ];
        $this->seeInDatabase("ConsultantInvitee", $consultantInvitationEntry);
    }

    public function test_show_200()
    {
        $response = [
            "id" => $this->participantActivity->id,
            "activityType" => [
                "id" => $this->participantActivity->activity->activityType->id,
                "name" => $this->participantActivity->activity->activityType->name,
            ],
            "name" => $this->participantActivity->activity->name,
            "description" => $this->participantActivity->activity->description,
            "startTime" => $this->participantActivity->activity->startDateTime,
            "endTime" => $this->participantActivity->activity->endDateTime,
            "location" => $this->participantActivity->activity->location,
            "note" => $this->participantActivity->activity->note,
            "cancelled" => $this->participantActivity->activity->cancelled,
        ];

        $uri = $this->activityUri . "/{$this->participantActivity->id}";
        $this->get($uri, $this->programParticipation->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }

    public function test_showAll_200()
    {
        $repsonse = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->participantActivity->id,
                    "activityType" => [
                        "id" => $this->participantActivity->activity->activityType->id,
                        "name" => $this->participantActivity->activity->activityType->name,
                    ],
                    "name" => $this->participantActivity->activity->name,
                    "description" => $this->participantActivity->activity->description,
                    "startTime" => $this->participantActivity->activity->startDateTime,
                    "endTime" => $this->participantActivity->activity->endDateTime,
                    "location" => $this->participantActivity->activity->location,
                    "note" => $this->participantActivity->activity->note,
                    "cancelled" => $this->participantActivity->activity->cancelled,
                ],
                [
                    "id" => $this->participantActivityOne->id,
                    "activityType" => [
                        "id" => $this->participantActivityOne->activity->activityType->id,
                        "name" => $this->participantActivityOne->activity->activityType->name,
                    ],
                    "name" => $this->participantActivityOne->activity->name,
                    "description" => $this->participantActivityOne->activity->description,
                    "startTime" => $this->participantActivityOne->activity->startDateTime,
                    "endTime" => $this->participantActivityOne->activity->endDateTime,
                    "location" => $this->participantActivityOne->activity->location,
                    "note" => $this->participantActivityOne->activity->note,
                    "cancelled" => $this->participantActivityOne->activity->cancelled,
                ],
            ],
        ];

        $this->get($this->activityUri, $this->programParticipation->client->token)
                ->seeJsonContains($repsonse)
                ->seeStatusCode(200);
    }

}
