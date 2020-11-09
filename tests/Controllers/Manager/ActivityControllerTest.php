<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Firm\ {
    Manager\RecordOfManagerActivity,
    Manager\RecordOfManagerInvitation,
    Program\Activity\RecordOfInvitation,
    Program\Consultant\RecordOfConsultantInvitation,
    Program\RecordOfActivity,
    Program\RecordOfActivityType,
    Program\RecordOfConsultant,
    Program\RecordOfCoordinator,
    Program\RecordOfParticipant,
    RecordOfManager,
    RecordOfPersonnel
};

class ActivityControllerTest extends ActivityTestCase
{
    protected $managerActivityOne;
    protected $managerOne;
    protected $coordinator;
    protected $consultantTwo;
    protected $consultantOne;
    protected $participant;
    protected $invitation;
    protected $invitationOne_consultantOne;
    protected $requestInput;
    protected $updateInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table("Invitation")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Invitation")->truncate();
        $this->connection->table("ManagerInvitation")->truncate();
        $this->connection->table("CoordinatorInvitation")->truncate();
        $this->connection->table("ConsultantInvitation")->truncate();
        $this->connection->table("ParticipantInvitation")->truncate();
        
        $program = $this->managerActivity->activity->program;
        
        $activityType = $this->managerActivity->activity->activityType;
        
        $activity = new RecordOfActivity($program, $activityType, 1);
        $this->connection->table("Activity")->insert($activity->toArrayForDbEntry());
        
        
        $this->managerActivityOne = new RecordOfManagerActivity($this->manager, $activity);
        $this->connection->table("ManagerActivity")->insert($this->managerActivityOne->toArrayForDbEntry());
        
        $this->managerOne = new RecordOfManager($this->firm, 11, "managerOne@email.org", "Password123");
        $this->connection->table("Manager")->insert($this->managerOne->toArrayForDbEntry());
        
        $personnel = new RecordOfPersonnel($this->firm, 0);
        $personnelOne = new RecordOfPersonnel($this->firm, 1);
        $personnelTwo = new RecordOfPersonnel($this->firm, 2);
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());
        $this->connection->table("Personnel")->insert($personnelOne->toArrayForDbEntry());
        $this->connection->table("Personnel")->insert($personnelTwo->toArrayForDbEntry());
        
        $this->coordinator = new RecordOfCoordinator($program, $personnel, 0);
        $this->connection->table("Coordinator")->insert($this->coordinator->toArrayForDbEntry());
        
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
        
        $managerInvitation = new RecordOfManagerInvitation($this->managerOne, $this->invitation);
        $this->connection->table("ManagerInvitation")->insert($managerInvitation->toArrayForDbEntry());
        
        $consultantInvitation = new RecordOfConsultantInvitation($this->consultantOne, $this->invitationOne_consultantOne);
        $this->connection->table("ConsultantInvitation")->insert($consultantInvitation->toArrayForDbEntry());
        
        $this->updateInput = [
            "name" => "new activity name",
            "description" => "new activity description",
            "location" => "new activity location",
            "note" => "new activity note",
            "startTime" => (new \DateTimeImmutable("+48 hours"))->format("Y-m-d H:i:s"),
            "endTime" => (new \DateTimeImmutable("+52 hours"))->format("Y-m-d H:i:s"),
            "invitedManagerList" => [
                $this->managerOne->id,
            ],
            "invitedCoordinatorList" => [
                $this->coordinator->id,
                
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
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Invitation")->truncate();
        $this->connection->table("ManagerInvitation")->truncate();
        $this->connection->table("CoordinatorInvitation")->truncate();
        $this->connection->table("ConsultantInvitation")->truncate();
        $this->connection->table("ParticipantInvitation")->truncate();
    }
    
    public function test_initiate_201()
    {
        $this->connection->table("ManagerActivity")->truncate();
        $this->connection->table("Invitation")->truncate();
        $this->connection->table("ManagerInvitation")->truncate();
        $this->connection->table("ConsultantInvitation")->truncate();
        
        $response = [
            "activityType" => [
                "id" => $this->managerActivity->activity->activityType->id,
                "name" => $this->managerActivity->activity->activityType->name,
            ],
            "name" => $this->requestInput["name"],
            "description" => $this->requestInput["description"],
            "startTime" => $this->requestInput["startTime"],
            "endTime" => $this->requestInput["endTime"],
            "location" => $this->requestInput["location"],
            "note" => $this->requestInput["note"],
            "cancelled" => false,
        ];
        
        $this->post($this->activityUri, $this->requestInput, $this->manager->token)
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
        
        $managerActivityEntry = [
            "Manager_id" => $this->manager->id,
        ];
        $this->seeInDatabase("ManagerActivity", $managerActivityEntry);
        
        $invitationEntry = [
            "removed" => false,
            "willAttend" => null,
            "attended" => null,
        ];
        $this->seeInDatabase("Invitation", $invitationEntry);
        
        $managerInvitationEntry = [
            "Manager_id" => $this->managerOne->id,
        ];
        $this->seeInDatabase("ManagerInvitation", $managerInvitationEntry);
                
        $coordinatorInvitationEntry = [
            "Coordinator_id" => $this->coordinator->id,
        ];
        $this->seeInDatabase("CoordinatorInvitation", $coordinatorInvitationEntry);
        
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
            "id" => $this->managerActivityOne->id,
            "name" => $this->updateInput["name"],
            "description" => $this->updateInput["description"],
            "startTime" => $this->updateInput["startTime"],
            "endTime" => $this->updateInput["endTime"],
            "location" => $this->updateInput["location"],
            "note" => $this->updateInput["note"],
            "cancelled" => false,
        ];
        
        $uri = $this->activityUri . "/{$this->managerActivityOne->id}";
        $this->patch($uri, $this->updateInput, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $activityEntry = [
            "id" => $this->managerActivityOne->activity->id,
            "name" => $this->updateInput["name"],
            "description" => $this->updateInput["description"],
            "location" => $this->updateInput["location"],
            "note" => $this->updateInput["note"],
            "startDateTime" => $this->updateInput["startTime"],
            "endDateTime" => $this->updateInput["endTime"],
        ];
        $this->seeInDatabase("Activity", $activityEntry);
        
        $coordinatorInvitationEntry = [
            "Coordinator_id" => $this->coordinator->id,
        ];
        $this->seeInDatabase("CoordinatorInvitation", $coordinatorInvitationEntry);
        
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
            "id" => $this->managerActivity->id,
            "activityType" => [
                "id" => $this->managerActivity->activity->activityType->id,
                "name" => $this->managerActivity->activity->activityType->name,
            ],
            "name" => $this->managerActivity->activity->name,
            "description" => $this->managerActivity->activity->description,
            "startTime" => $this->managerActivity->activity->startDateTime,
            "endTime" => $this->managerActivity->activity->endDateTime,
            "location" => $this->managerActivity->activity->location,
            "note" => $this->managerActivity->activity->note,
            "cancelled" => $this->managerActivity->activity->cancelled,
        ];
        
        $uri = $this->activityUri . "/{$this->managerActivity->id}";
        $this->get($uri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_200()
    {
        $repsonse = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->managerActivity->id,
                    "activityType" => [
                        "id" => $this->managerActivity->activity->activityType->id,
                        "name" => $this->managerActivity->activity->activityType->name,
                    ],
                    "name" => $this->managerActivity->activity->name,
                    "description" => $this->managerActivity->activity->description,
                    "startTime" => $this->managerActivity->activity->startDateTime,
                    "endTime" => $this->managerActivity->activity->endDateTime,
                    "location" => $this->managerActivity->activity->location,
                    "note" => $this->managerActivity->activity->note,
                    "cancelled" => $this->managerActivity->activity->cancelled,
                ],
                [
                    "id" => $this->managerActivityOne->id,
                    "activityType" => [
                        "id" => $this->managerActivityOne->activity->activityType->id,
                        "name" => $this->managerActivityOne->activity->activityType->name,
                    ],
                    "name" => $this->managerActivityOne->activity->name,
                    "description" => $this->managerActivityOne->activity->description,
                    "startTime" => $this->managerActivityOne->activity->startDateTime,
                    "endTime" => $this->managerActivityOne->activity->endDateTime,
                    "location" => $this->managerActivityOne->activity->location,
                    "note" => $this->managerActivityOne->activity->note,
                    "cancelled" => $this->managerActivityOne->activity->cancelled,
                ],
            ],
        ];
        
        $this->get($this->activityUri, $this->manager->token)
                ->seeJsonContains($repsonse)
                ->seeStatusCode(200);
    }
}
