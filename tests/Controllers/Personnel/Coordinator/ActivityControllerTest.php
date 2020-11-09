<?php

namespace Tests\Controllers\Personnel\Coordinator;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Firm\ {
    Program\Activity\RecordOfInvitation,
    Program\Consultant\RecordOfConsultantInvitation,
    Program\Coordinator\RecordOfCoordinatorActivity,
    Program\Coordinator\RecordOfCoordinatorInvitation,
    Program\RecordOfActivity,
    Program\RecordOfConsultant,
    Program\RecordOfCoordinator,
    Program\RecordOfParticipant,
    RecordOfManager,
    RecordOfPersonnel
};

class ActivityControllerTest extends ActivityTestCase
{

    protected $coordinatorActivityOne;
    protected $coordinatorOne;
    protected $manager;
    protected $consultantOne;
    protected $consultantTwo;
    protected $participant;
    protected $invitation;
    protected $invitationOne_consultantOne;
    protected $requestInput;
    protected $updateInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table("Invitation")->truncate();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Invitation")->truncate();
        $this->connection->table("CoordinatorInvitation")->truncate();
        $this->connection->table("ManagerInvitation")->truncate();
        $this->connection->table("ConsultantInvitation")->truncate();
        $this->connection->table("ParticipantInvitation")->truncate();

        $program = $this->coordinator->program;
        $firm = $program->firm;
        $activityType = $this->coordinatorActivity->activity->activityType;

        $personnel = new RecordOfPersonnel($firm, 0);
        $personnelOne = new RecordOfPersonnel($firm, 1);
        $personnelTwo = new RecordOfPersonnel($firm, 2);
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());
        $this->connection->table("Personnel")->insert($personnelOne->toArrayForDbEntry());
        $this->connection->table("Personnel")->insert($personnelTwo->toArrayForDbEntry());
        
        $activity = new RecordOfActivity($program, $activityType, 1);
        $this->connection->table("Activity")->insert($activity->toArrayForDbEntry());

        $this->coordinatorActivityOne = new RecordOfCoordinatorActivity($this->coordinator, $activity);
        $this->connection->table("CoordinatorActivity")->insert($this->coordinatorActivityOne->toArrayForDbEntry());

        $this->coordinatorOne = new RecordOfCoordinator($program, $personnel, 0);
        $this->connection->table("Coordinator")->insert($this->coordinatorOne->toArrayForDbEntry());

        $this->manager = new RecordOfManager($firm, 0, "manager@email.org", "Passwrod123");
        $this->connection->table("Manager")->insert($this->manager->toArrayForDbEntry());

        $this->consultantOne = new RecordOfConsultant($program, $personnelOne, 1);
        $this->consultantTwo = new RecordOfConsultant($program, $personnelTwo, 2);
        $this->connection->table("Consultant")->insert($this->consultantOne->toArrayForDbEntry());
        $this->connection->table("Consultant")->insert($this->consultantTwo->toArrayForDbEntry());

        $this->participant = new RecordOfParticipant($program, 0);
        $this->connection->table("Participant")->insert($this->participant->toArrayForDbEntry());

        $this->invitation = new RecordOfInvitation($activity, 0);
        $this->invitationOne_consultantOne = new RecordOfInvitation($activity, 1);
        $this->connection->table("Invitation")->insert($this->invitation->toArrayForDbEntry());
        $this->connection->table("Invitation")->insert($this->invitationOne_consultantOne->toArrayForDbEntry());

        $coordinatorInvitation = new RecordOfCoordinatorInvitation($this->coordinatorOne, $this->invitation);
        $this->connection->table("CoordinatorInvitation")->insert($coordinatorInvitation->toArrayForDbEntry());

        $consultantInvitation = new RecordOfConsultantInvitation($this->consultantOne,
                $this->invitationOne_consultantOne);
        $this->connection->table("ConsultantInvitation")->insert($consultantInvitation->toArrayForDbEntry());

        $this->updateInput = [
            "name" => "new activity name",
            "description" => "new activity description",
            "location" => "new activity location",
            "note" => "new activity note",
            "startTime" => (new DateTimeImmutable("+48 hours"))->format("Y-m-d H:i:s"),
            "endTime" => (new DateTimeImmutable("+52 hours"))->format("Y-m-d H:i:s"),
            "invitedCoordinatorList" => [
                $this->coordinatorOne->id,
            ],
            "invitedManagerList" => [
                $this->manager->id,
            ],
            "invitedConsultantList" => [
                $this->consultantTwo->id,
            ],
            "invitedParticipantList" => [
                $this->participant->id,
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
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Invitation")->truncate();
        $this->connection->table("CoordinatorInvitation")->truncate();
        $this->connection->table("ManagerInvitation")->truncate();
        $this->connection->table("ConsultantInvitation")->truncate();
        $this->connection->table("ParticipantInvitation")->truncate();
    }

    public function test_initiate_201()
    {
$this->disableExceptionHandling();
        $this->connection->table("CoordinatorActivity")->truncate();
        $this->connection->table("Invitation")->truncate();
        $this->connection->table("CoordinatorInvitation")->truncate();
        $this->connection->table("ConsultantInvitation")->truncate();

        $response = [
            "activityType" => [
                "id" => $this->coordinatorActivity->activity->activityType->id,
                "name" => $this->coordinatorActivity->activity->activityType->name,
            ],
            "name" => $this->requestInput["name"],
            "description" => $this->requestInput["description"],
            "startTime" => $this->requestInput["startTime"],
            "endTime" => $this->requestInput["endTime"],
            "location" => $this->requestInput["location"],
            "note" => $this->requestInput["note"],
            "cancelled" => false,
        ];

        $this->post($this->activityUri, $this->requestInput, $this->coordinator->personnel->token)
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

        $coordinatorActivityEntry = [
            "Coordinator_id" => $this->coordinator->id,
        ];
        $this->seeInDatabase("CoordinatorActivity", $coordinatorActivityEntry);

        $invitationEntry = [
            "removed" => false,
            "willAttend" => null,
            "attended" => null,
        ];
        $this->seeInDatabase("Invitation", $invitationEntry);

        $coordinatorInvitationEntry = [
            "Coordinator_id" => $this->coordinatorOne->id,
        ];
        $this->seeInDatabase("CoordinatorInvitation", $coordinatorInvitationEntry);

        $managerInvitationEntry = [
            "Manager_id" => $this->manager->id,
        ];
        $this->seeInDatabase("ManagerInvitation", $managerInvitationEntry);

        $consultantInvitationEntry = [
            "Consultant_id" => $this->consultantTwo->id,
        ];
        $this->seeInDatabase("ConsultantInvitation", $consultantInvitationEntry);

        $participantInvitationEntry = [
            "Participant_id" => $this->participant->id,
        ];
        $this->seeInDatabase("ParticipantInvitation", $participantInvitationEntry);
    }

    public function test_update_200()
    {
        $response = [
            "id" => $this->coordinatorActivityOne->id,
            "name" => $this->updateInput["name"],
            "description" => $this->updateInput["description"],
            "startTime" => $this->updateInput["startTime"],
            "endTime" => $this->updateInput["endTime"],
            "location" => $this->updateInput["location"],
            "note" => $this->updateInput["note"],
            "cancelled" => false,
        ];

        $uri = $this->activityUri . "/{$this->coordinatorActivityOne->id}";
        $this->patch($uri, $this->updateInput, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);

        $activityEntry = [
            "id" => $this->coordinatorActivityOne->activity->id,
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
        $this->seeInDatabase("ManagerInvitation", $managerInvitationEntry);

        $consultantInvitationEntry = [
            "Consultant_id" => $this->consultantTwo->id,
        ];
        $this->seeInDatabase("ConsultantInvitation", $consultantInvitationEntry);

        $participantInvitationEntry = [
            "Participant_id" => $this->participant->id,
        ];
        $this->seeInDatabase("ParticipantInvitation", $participantInvitationEntry);
    }

    public function test_show_200()
    {
        $response = [
            "id" => $this->coordinatorActivity->id,
            "activityType" => [
                "id" => $this->coordinatorActivity->activity->activityType->id,
                "name" => $this->coordinatorActivity->activity->activityType->name,
            ],
            "name" => $this->coordinatorActivity->activity->name,
            "description" => $this->coordinatorActivity->activity->description,
            "startTime" => $this->coordinatorActivity->activity->startDateTime,
            "endTime" => $this->coordinatorActivity->activity->endDateTime,
            "location" => $this->coordinatorActivity->activity->location,
            "note" => $this->coordinatorActivity->activity->note,
            "cancelled" => $this->coordinatorActivity->activity->cancelled,
        ];

        $uri = $this->activityUri . "/{$this->coordinatorActivity->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }

    public function test_showAll_200()
    {
        $repsonse = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->coordinatorActivity->id,
                    "activityType" => [
                        "id" => $this->coordinatorActivity->activity->activityType->id,
                        "name" => $this->coordinatorActivity->activity->activityType->name,
                    ],
                    "name" => $this->coordinatorActivity->activity->name,
                    "description" => $this->coordinatorActivity->activity->description,
                    "startTime" => $this->coordinatorActivity->activity->startDateTime,
                    "endTime" => $this->coordinatorActivity->activity->endDateTime,
                    "location" => $this->coordinatorActivity->activity->location,
                    "note" => $this->coordinatorActivity->activity->note,
                    "cancelled" => $this->coordinatorActivity->activity->cancelled,
                ],
                [
                    "id" => $this->coordinatorActivityOne->id,
                    "activityType" => [
                        "id" => $this->coordinatorActivityOne->activity->activityType->id,
                        "name" => $this->coordinatorActivityOne->activity->activityType->name,
                    ],
                    "name" => $this->coordinatorActivityOne->activity->name,
                    "description" => $this->coordinatorActivityOne->activity->description,
                    "startTime" => $this->coordinatorActivityOne->activity->startDateTime,
                    "endTime" => $this->coordinatorActivityOne->activity->endDateTime,
                    "location" => $this->coordinatorActivityOne->activity->location,
                    "note" => $this->coordinatorActivityOne->activity->note,
                    "cancelled" => $this->coordinatorActivityOne->activity->cancelled,
                ],
            ],
        ];

        $this->get($this->activityUri, $this->coordinator->personnel->token)
                ->seeJsonContains($repsonse)
                ->seeStatusCode(200);
    }

}
