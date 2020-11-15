<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Firm\ {
    Program\Activity\RecordOfInvitee,
    Program\Consultant\RecordOfActivityInvitation,
    Program\Consultant\RecordOfConsultantActivity,
    Program\Coordinator\RecordOfActivityInvitation as RecordOfActivityInvitation2,
    Program\RecordOfActivity,
    Program\RecordOfConsultant,
    Program\RecordOfCoordinator,
    Program\RecordOfParticipant,
    RecordOfManager,
    RecordOfPersonnel
};

class ActivityControllerTest extends ActivityTestCase
{

    protected $consultantActivityOne;
    protected $consultantOne;
    protected $manager;
    protected $coordinatorOne;
    protected $coordinatorTwo;
    protected $participant;
    protected $invitation;
    protected $invitationOne_coordinatorOne;
    protected $requestInput;
    protected $updateInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();

        $program = $this->programConsultation->program;
        $firm = $program->firm;
        $activityType = $this->consultantActivity->activity->activityType;

        $personnel = new RecordOfPersonnel($firm, 0);
        $personnelOne = new RecordOfPersonnel($firm, 1);
        $personnelTwo = new RecordOfPersonnel($firm, 2);
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());
        $this->connection->table("Personnel")->insert($personnelOne->toArrayForDbEntry());
        $this->connection->table("Personnel")->insert($personnelTwo->toArrayForDbEntry());
        
        $activity = new RecordOfActivity($program, $activityType, 1);
        $this->connection->table("Activity")->insert($activity->toArrayForDbEntry());

        $this->consultantActivityOne = new RecordOfConsultantActivity($this->programConsultation, $activity);
        $this->connection->table("ConsultantActivity")->insert($this->consultantActivityOne->toArrayForDbEntry());

        $this->consultantOne = new RecordOfConsultant($program, $personnel, 0);
        $this->connection->table("Consultant")->insert($this->consultantOne->toArrayForDbEntry());

        $this->manager = new RecordOfManager($firm, 0, "manager@email.org", "Passwrod123");
        $this->connection->table("Manager")->insert($this->manager->toArrayForDbEntry());

        $this->coordinatorOne = new RecordOfCoordinator($program, $personnelOne, 1);
        $this->coordinatorTwo = new RecordOfCoordinator($program, $personnelTwo, 2);
        $this->connection->table("Coordinator")->insert($this->coordinatorOne->toArrayForDbEntry());
        $this->connection->table("Coordinator")->insert($this->coordinatorTwo->toArrayForDbEntry());

        $this->participant = new RecordOfParticipant($program, 0);
        $this->connection->table("Participant")->insert($this->participant->toArrayForDbEntry());

        $this->invitation = new RecordOfInvitee($activity, $this->activityParticipant_consultant, 0);
        $this->invitationOne_coordinatorOne = new RecordOfInvitee($activity, $this->activityParticipant_consultant, 1);
        $this->connection->table("Invitee")->insert($this->invitation->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($this->invitationOne_coordinatorOne->toArrayForDbEntry());

        $consultantInvitation = new RecordOfActivityInvitation($this->consultantOne, $this->invitation);
        $this->connection->table("ConsultantInvitee")->insert($consultantInvitation->toArrayForDbEntry());

        $coordinatorInvitation = new RecordOfActivityInvitation2($this->coordinatorOne, $this->invitationOne_coordinatorOne);
        $this->connection->table("CoordinatorInvitee")->insert($coordinatorInvitation->toArrayForDbEntry());

        $this->updateInput = [
            "name" => "new activity name",
            "description" => "new activity description",
            "location" => "new activity location",
            "note" => "new activity note",
            "startTime" => (new DateTimeImmutable("+48 hours"))->format("Y-m-d H:i:s"),
            "endTime" => (new DateTimeImmutable("+52 hours"))->format("Y-m-d H:i:s"),
            "invitedConsultantList" => [
                $this->consultantOne->id,
            ],
            "invitedManagerList" => [
                $this->manager->id,
            ],
            "invitedCoordinatorList" => [
                $this->coordinatorTwo->id,
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
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
    }

    public function test_initiate_201()
    {
        $this->connection->table("ConsultantActivity")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();

        $response = [
            "activityType" => [
                "id" => $this->consultantActivity->activity->activityType->id,
                "name" => $this->consultantActivity->activity->activityType->name,
            ],
            "name" => $this->requestInput["name"],
            "description" => $this->requestInput["description"],
            "startTime" => $this->requestInput["startTime"],
            "endTime" => $this->requestInput["endTime"],
            "location" => $this->requestInput["location"],
            "note" => $this->requestInput["note"],
            "cancelled" => false,
        ];

        $this->post($this->activityUri, $this->requestInput, $this->programConsultation->personnel->token)
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

        $consultantActivityEntry = [
            "Consultant_id" => $this->programConsultation->id,
        ];
        $this->seeInDatabase("ConsultantActivity", $consultantActivityEntry);

        $invitationEntry = [
            "invitationCancelled" => false,
            "willAttend" => null,
            "attended" => null,
        ];
        $this->seeInDatabase("Invitee", $invitationEntry);

        $consultantInvitationEntry = [
            "Consultant_id" => $this->consultantOne->id,
        ];
        $this->seeInDatabase("ConsultantInvitee", $consultantInvitationEntry);

        $managerInvitationEntry = [
            "Manager_id" => $this->manager->id,
        ];
        $this->seeInDatabase("ManagerInvitee", $managerInvitationEntry);

        $coordinatorInvitationEntry = [
            "Coordinator_id" => $this->coordinatorTwo->id,
        ];
        $this->seeInDatabase("CoordinatorInvitee", $coordinatorInvitationEntry);

        $participantInvitationEntry = [
            "Participant_id" => $this->participant->id,
        ];
        $this->seeInDatabase("ParticipantInvitee", $participantInvitationEntry);
    }

    public function test_update_200()
    {
        $response = [
            "id" => $this->consultantActivityOne->id,
            "name" => $this->updateInput["name"],
            "description" => $this->updateInput["description"],
            "startTime" => $this->updateInput["startTime"],
            "endTime" => $this->updateInput["endTime"],
            "location" => $this->updateInput["location"],
            "note" => $this->updateInput["note"],
            "cancelled" => false,
        ];

        $uri = $this->activityUri . "/{$this->consultantActivityOne->id}";
        $this->patch($uri, $this->updateInput, $this->programConsultation->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);

        $activityEntry = [
            "id" => $this->consultantActivityOne->activity->id,
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

        $participantInvitationEntry = [
            "Participant_id" => $this->participant->id,
        ];
        $this->seeInDatabase("ParticipantInvitee", $participantInvitationEntry);
    }

    public function test_show_200()
    {
        $response = [
            "id" => $this->consultantActivity->id,
            "activityType" => [
                "id" => $this->consultantActivity->activity->activityType->id,
                "name" => $this->consultantActivity->activity->activityType->name,
            ],
            "name" => $this->consultantActivity->activity->name,
            "description" => $this->consultantActivity->activity->description,
            "startTime" => $this->consultantActivity->activity->startDateTime,
            "endTime" => $this->consultantActivity->activity->endDateTime,
            "location" => $this->consultantActivity->activity->location,
            "note" => $this->consultantActivity->activity->note,
            "cancelled" => $this->consultantActivity->activity->cancelled,
        ];

        $uri = $this->activityUri . "/{$this->consultantActivity->id}";
        $this->get($uri, $this->programConsultation->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }

    public function test_showAll_200()
    {
        $repsonse = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->consultantActivity->id,
                    "activityType" => [
                        "id" => $this->consultantActivity->activity->activityType->id,
                        "name" => $this->consultantActivity->activity->activityType->name,
                    ],
                    "name" => $this->consultantActivity->activity->name,
                    "description" => $this->consultantActivity->activity->description,
                    "startTime" => $this->consultantActivity->activity->startDateTime,
                    "endTime" => $this->consultantActivity->activity->endDateTime,
                    "location" => $this->consultantActivity->activity->location,
                    "note" => $this->consultantActivity->activity->note,
                    "cancelled" => $this->consultantActivity->activity->cancelled,
                ],
                [
                    "id" => $this->consultantActivityOne->id,
                    "activityType" => [
                        "id" => $this->consultantActivityOne->activity->activityType->id,
                        "name" => $this->consultantActivityOne->activity->activityType->name,
                    ],
                    "name" => $this->consultantActivityOne->activity->name,
                    "description" => $this->consultantActivityOne->activity->description,
                    "startTime" => $this->consultantActivityOne->activity->startDateTime,
                    "endTime" => $this->consultantActivityOne->activity->endDateTime,
                    "location" => $this->consultantActivityOne->activity->location,
                    "note" => $this->consultantActivityOne->activity->note,
                    "cancelled" => $this->consultantActivityOne->activity->cancelled,
                ],
            ],
        ];

        $this->get($this->activityUri, $this->programConsultation->personnel->token)
                ->seeJsonContains($repsonse)
                ->seeStatusCode(200);
    }

}
