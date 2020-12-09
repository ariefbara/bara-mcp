<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivity;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivityType;

class ActivityControllerTest extends AsProgramCoordinatorTestCase
{
    protected $activityUri;
    protected $activityOne;
    protected $activityTwo;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->activityUri = $this->asProgramCoordinatorUri . "/activities";
        
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        
        $program = $this->coordinator->program;
        
        $activityType = new RecordOfActivityType($program, 0);
        $this->connection->table("ActivityType")->insert($activityType->toArrayForDbEntry());
        
        $this->activityOne = new RecordOfActivity($activityType, 1);
        $this->activityTwo = new RecordOfActivity($activityType, 2);
        $this->connection->table("Activity")->insert($this->activityOne->toArrayForDbEntry());
        $this->connection->table("Activity")->insert($this->activityTwo->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->activityOne->id,
            "name" => $this->activityOne->name,
            "description" => $this->activityOne->description,
            "startTime" => $this->activityOne->startDateTime,
            "endTime" => $this->activityOne->endDateTime,
            "location" => $this->activityOne->location,
            "note" => $this->activityOne->note,
            "cancelled" => $this->activityOne->cancelled,
            "createdTime" => $this->activityOne->createdTime,
        ];
        $uri = $this->activityUri . "/{$this->activityOne->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->activityOne->id,
                    "name" => $this->activityOne->name,
                    "startTime" => $this->activityOne->startDateTime,
                    "endTime" => $this->activityOne->endDateTime,
                    "cancelled" => $this->activityOne->cancelled,
                ],
                [
                    "id" => $this->activityTwo->id,
                    "name" => $this->activityTwo->name,
                    "startTime" => $this->activityTwo->startDateTime,
                    "endTime" => $this->activityTwo->endDateTime,
                    "cancelled" => $this->activityTwo->cancelled,
                ],
            ],
        ];
        $this->get($this->activityUri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
