<?php

namespace Tests\Controllers\Manager\Program;

use Tests\Controllers\ {
    Manager\ProgramTestCase,
    RecordPreparation\Firm\Program\Activity\RecordOfInvitee,
    RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant,
    RecordPreparation\Firm\Program\Coordinator\RecordOfActivityInvitation,
    RecordPreparation\Firm\Program\RecordOfActivity,
    RecordPreparation\Firm\Program\RecordOfActivityType,
    RecordPreparation\Firm\Program\RecordOfCoordinator,
    RecordPreparation\Firm\RecordOfPersonnel
};

class CoordinatorControllerTest extends ProgramTestCase
{
    protected $coordinatorUri;
    protected $coordinator, $coordinator1;
    protected $invitee;
    protected $personnel, $personnel1, $personnel2;
    protected $coordinatorInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinatorUri = $this->programUri . "/{$this->program->id}/coordinators";
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Coordinator')->truncate();
        
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        
        $this->personnel = new RecordOfPersonnel($this->firm, 0, 'personnel@email.org', 'password123');
        $this->personnel1 = new RecordOfPersonnel($this->firm, 1, 'personnel1@email.org', 'password123');
        $this->personnel2 = new RecordOfPersonnel($this->firm, 2, 'personnel2@email.org', 'password123');
        $this->connection->table('Personnel')->insert($this->personnel->toArrayForDbEntry());
        $this->connection->table('Personnel')->insert($this->personnel1->toArrayForDbEntry());
        $this->connection->table('Personnel')->insert($this->personnel2->toArrayForDbEntry());
        
        $this->coordinator = new RecordOfCoordinator($this->program, $this->personnel, 0);
        $this->coordinator1 = new RecordOfCoordinator($this->program, $this->personnel1, 1);
        $this->connection->table('Coordinator')->insert($this->coordinator->toArrayForDbEntry());
        $this->connection->table('Coordinator')->insert($this->coordinator1->toArrayForDbEntry());
        
        $activityType = new RecordOfActivityType($this->program, 0);
        $this->connection->table("ActivityType")->insert($activityType->toArrayForDbEntry());
        
        $activity = new RecordOfActivity($activityType, 0);
        $this->connection->table("Activity")->insert($activity->toArrayForDbEntry());
        
        $activityParticipant = new RecordOfActivityParticipant($activityType, null, 0);
        $this->connection->table("ActivityParticipant")->insert($activityParticipant->toArrayForDbEntry());
        
        $this->invitee = new RecordOfInvitee($activity, $activityParticipant, 0);
        $this->connection->table("Invitee")->insert($this->invitee->toArrayForDbEntry());
        
        $coordinatorInvitee = new RecordOfActivityInvitation($this->coordinator, $this->invitee);
        $this->connection->table("CoordinatorInvitee")->insert($coordinatorInvitee->toArrayForDbEntry());
        
        $this->coordinatorInput = [
            "personnelId" => $this->personnel2->id,
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Coordinator')->truncate();
        
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
    }
    
    public function test_assign()
    {
        $response = [
            "personnel" => [
                "id" => $this->personnel2->id,
                "name" => $this->personnel2->getFullName(),
            ],
        ];
        $this->put($this->coordinatorUri, $this->coordinatorInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $coordinatorRecord = [
            "Program_id" => $this->program->id,
            "Personnel_id" => $this->personnel2->id,
            "active" => true,
        ];
        $this->seeInDatabase('Coordinator', $coordinatorRecord);
    }
    public function test_assign_userNotManager_error401()
    {
        $this->put($this->coordinatorUri, $this->coordinatorInput, $this->removedManager->token)
            ->seeStatusCode(401);
    }
    public function test_assign_inactivePersonnel_403()
    {
        $personnel = new RecordOfPersonnel($this->manager->firm, 99);
        $personnel->active = false;
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());
        
        $this->put($this->coordinatorUri, ["personnelId" => $personnel->id], $this->manager->token)
            ->seeStatusCode(403);
    }
    
    public function test_disable_200()
    {
        $uri = $this->coordinatorUri . "/{$this->coordinator->id}";
        $this->delete($uri, [], $this->manager->token)
            ->seeStatusCode(200);
        
        $coordinatorRecord = [
            "id" => $this->coordinator->id,
            "active" => false,
        ];
        $this->seeInDatabase('Coordinator', $coordinatorRecord);
    }
    public function test_disable_disableAggregatedInvitation()
    {
        $uri = $this->coordinatorUri . "/{$this->coordinator->id}";
        $this->delete($uri, [], $this->manager->token)
            ->seeStatusCode(200);
        
        $inviteeEntry = [
            "id" => $this->invitee->id,
            "cancelled" => true,
        ];
        $this->seeInDatabase("Invitee", $inviteeEntry);
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->coordinator->id,
            "personnel" => [
                "id" => $this->coordinator->personnel->id,
                "name" => $this->coordinator->personnel->getFullName(),
            ],
        ];
        $uri = $this->coordinatorUri . "/{$this->coordinator->id}";
        $this->get($uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_show_userNotManager_error401()
    {
        $uri = $this->coordinatorUri . "/{$this->coordinator->id}";
        $this->get($uri, $this->removedManager->token)
            ->seeStatusCode(401);
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->coordinator->id,
                    "personnel" => [
                        "id" => $this->coordinator->personnel->id,
                        "name" => $this->coordinator->personnel->getFullName(),
                    ],
                ],
                [
                    "id" => $this->coordinator1->id,
                    "personnel" => [
                        "id" => $this->coordinator1->personnel->id,
                        "name" => $this->coordinator1->personnel->getFullName(),
                    ],
                ],
            ],
        ];
        
        $this->get($this->coordinatorUri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_showAll_userNotManager_error401()
    {
        $this->get($this->coordinatorUri, $this->removedManager->token)
            ->seeStatusCode(401);
    }
}
