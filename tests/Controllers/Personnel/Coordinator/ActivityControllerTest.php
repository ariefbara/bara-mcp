<?php

namespace Tests\Controllers\Manager;

use DateTimeImmutable;
use Tests\Controllers\ {
    Personnel\Coordinator\ActivityTestCase,
    RecordPreparation\Firm\Manager\RecordOfActivityInvitation as ManagerInvitee,
    RecordPreparation\Firm\Program\Activity\RecordOfInvitee,
    RecordPreparation\Firm\Program\Consultant\RecordOfActivityInvitation as ConsultantInvitee,
    RecordPreparation\Firm\Program\Coordinator\RecordOfCoordinatorActivity,
    RecordPreparation\Firm\Program\RecordOfActivity,
    RecordPreparation\Firm\Program\RecordOfConsultant,
    RecordPreparation\Firm\Program\RecordOfCoordinator,
    RecordPreparation\Firm\Program\RecordOfParticipant,
    RecordPreparation\Firm\RecordOfManager,
    RecordPreparation\Firm\RecordOfPersonnel
};

class ActivityControllerTest extends ActivityTestCase
{
    protected $coordinatorActivityOne;
    protected $managerOne;
    protected $coordinatorOne;
    protected $consultantTwo;
    protected $consultantOne;
    protected $participant;
    protected $invitee;
    protected $inviteeOne_consultantOne;
    protected $requestInput;
    protected $updateInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
        
        $program = $this->coordinator->program;
        $firm = $program->firm;
        
        $activity = new RecordOfActivity($program, $this->activityType, 1);
        $this->connection->table("Activity")->insert($activity->toArrayForDbEntry());
        
        $this->coordinatorActivityOne = new RecordOfCoordinatorActivity($this->coordinator, $activity);
        $this->connection->table("CoordinatorActivity")->insert($this->coordinatorActivityOne->toArrayForDbEntry());
        
        $this->managerOne = new RecordOfManager($firm, 11, "managerOne@email.org", "Password123");
        $this->connection->table("Manager")->insert($this->managerOne->toArrayForDbEntry());
        
        $personnel = new RecordOfPersonnel($firm, 0);
        $personnelOne = new RecordOfPersonnel($firm, 1);
        $personnelTwo = new RecordOfPersonnel($firm, 2);
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());
        $this->connection->table("Personnel")->insert($personnelOne->toArrayForDbEntry());
        $this->connection->table("Personnel")->insert($personnelTwo->toArrayForDbEntry());
        
        $this->coordinatorOne = new RecordOfCoordinator($program, $personnel, 1);
        $this->connection->table("Coordinator")->insert($this->coordinatorOne->toArrayForDbEntry());
        
        $this->consultantOne = new RecordOfConsultant($program, $personnelOne, 1);
        $this->consultantTwo = new RecordOfConsultant($program, $personnelTwo, 2);
        $this->connection->table("Consultant")->insert($this->consultantOne->toArrayForDbEntry());
        $this->connection->table("Consultant")->insert($this->consultantTwo->toArrayForDbEntry());
        
        $this->participant = new RecordOfParticipant($program, 0);
        $this->connection->table("Participant")->insert($this->participant->toArrayForDbEntry());
        
        $this->Invitee = new RecordOfInvitee($activity, $this->activityParticipantOne_Manager, 0);
        $this->InviteeOne_consultantOne = new RecordOfInvitee($activity, $this->activityParticipantTwo_Consultant, 1);
        $this->connection->table("Invitee")->insert($this->Invitee->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($this->InviteeOne_consultantOne->toArrayForDbEntry());
        
        $managerInvitee = new ManagerInvitee($this->managerOne, $this->Invitee);
        $this->connection->table("ManagerInvitee")->insert($managerInvitee->toArrayForDbEntry());
        
        $consultantInvitee = new ConsultantInvitee($this->consultantOne, $this->InviteeOne_consultantOne);
        $this->connection->table("ConsultantInvitee")->insert($consultantInvitee->toArrayForDbEntry());
        
        $this->updateInput = [
            "name" => "new activity name",
            "description" => "new activity description",
            "location" => "new activity location",
            "note" => "new activity note",
            "startTime" => (new DateTimeImmutable("+48 hours"))->format("Y-m-d H:i:s"),
            "endTime" => (new DateTimeImmutable("+52 hours"))->format("Y-m-d H:i:s"),
            "invitedManagerList" => [
                $this->managerOne->id,
            ],
            "invitedCoordinatorList" => [
                $this->coordinatorOne->id,
                
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
        $this->requestInput["activityTypeId"] = $this->activityType->id;
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
    }
    
    public function test_initiate_201()
    {
        $this->connection->table("ManagerActivity")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
        
        $response = [
            "activityType" => [
                "id" => $this->activityType->id,
                "name" => $this->activityType->name,
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
        
        $inviteeEntry = [
            "ActivityParticipant_id" => $this->activityParticipant_coordinator->id,
            "invitationCancelled" => false,
            "willAttend" => null,
            "attended" => null,
        ];
        $this->seeInDatabase("Invitee", $inviteeEntry);
        
        $inviteeOneEntry = [
            "ActivityParticipant_id" => $this->activityParticipantOne_Manager->id,
            "invitationCancelled" => false,
            "willAttend" => null,
            "attended" => null,
        ];
        $this->seeInDatabase("Invitee", $inviteeOneEntry);
        
        $inviteeTwoEntry = [
            "ActivityParticipant_id" => $this->activityParticipantTwo_Consultant->id,
            "invitationCancelled" => false,
            "willAttend" => null,
            "attended" => null,
        ];
        $this->seeInDatabase("Invitee", $inviteeTwoEntry);
        
        $inviteeThreeEntry = [
            "ActivityParticipant_id" => $this->activityParticipantThree_Participant->id,
            "invitationCancelled" => false,
            "willAttend" => null,
            "attended" => null,
        ];
        $this->seeInDatabase("Invitee", $inviteeThreeEntry);
        
        $managerInviteeEntry = [
            "Manager_id" => $this->managerOne->id,
        ];
        $this->seeInDatabase("ManagerInvitee", $managerInviteeEntry);
                
        $coordinatorInviteeEntry = [
            "Coordinator_id" => $this->coordinatorOne->id,
        ];
        $this->seeInDatabase("CoordinatorInvitee", $coordinatorInviteeEntry);
        
        $consultantInviteeEntry = [
            "Consultant_id" => $this->consultantTwo->id,
        ];
        $this->seeInDatabase("ConsultantInvitee", $consultantInviteeEntry);
        
        $participantInviteeEntry = [
            "Participant_id" => $this->participant->id,
        ];
        $this->seeInDatabase("ParticipantInvitee", $participantInviteeEntry);
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
        
        $coordinatorInviteeEntry = [
            "Coordinator_id" => $this->coordinatorOne->id,
        ];
        $this->seeInDatabase("CoordinatorInvitee", $coordinatorInviteeEntry);
        
        $consultantInviteeEntry = [
            "Consultant_id" => $this->consultantTwo->id,
        ];
        $this->seeInDatabase("ConsultantInvitee", $consultantInviteeEntry);
        
        $participantInviteeEntry = [
            "Participant_id" => $this->participant->id,
        ];
        $this->seeInDatabase("ParticipantInvitee", $participantInviteeEntry);
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
