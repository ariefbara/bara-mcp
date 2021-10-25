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
        
        $activityTypeOne = new RecordOfActivityType($program, 1);
        $activityTypeTwo = new RecordOfActivityType($program, 2);
        
        $this->activityOne = new RecordOfActivity($activityTypeOne, 1);
        $this->activityTwo = new RecordOfActivity($activityTypeTwo, 2);
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
    }
    
    protected function show()
    {
        $this->activityOne->activityType->insert($this->connection);
        $this->activityOne->insert($this->connection);
        
        $uri = $this->activityUri . "/{$this->activityOne->id}";
        $this->get($uri, $this->personnel->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
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
        $this->seeJsonContains($response);
    }
    
    protected function showAll()
    {
        $this->activityOne->activityType->insert($this->connection);
        $this->activityTwo->activityType->insert($this->connection);
        
        $this->activityOne->insert($this->connection);
        $this->activityTwo->insert($this->connection);
        $this->get($this->activityUri, $this->personnel->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 2];
        
        $activityOneResponse = [
            "id" => $this->activityOne->id,
            "name" => $this->activityOne->name,
            "startTime" => $this->activityOne->startDateTime,
            "endTime" => $this->activityOne->endDateTime,
            "cancelled" => $this->activityOne->cancelled,
        ];
        $this->seeJsonContains($activityOneResponse);
        
        $activityTwoResponse = [
            "id" => $this->activityTwo->id,
            "name" => $this->activityTwo->name,
            "startTime" => $this->activityTwo->startDateTime,
            "endTime" => $this->activityTwo->endDateTime,
            "cancelled" => $this->activityTwo->cancelled,
        ];
        $this->seeJsonContains($activityTwoResponse);
    }
    public function test_showAll_filterForm()
    {
        $this->activityOne->startDateTime = (new \DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s');
        $this->activityOne->endDateTime = (new \DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s');
        $this->activityTwo->startDateTime = (new \DateTimeImmutable('-24 hours'))->format('Y-m-d H:i:s');
        $this->activityTwo->endDateTime = (new \DateTimeImmutable('-23 hours'))->format('Y-m-d H:i:s');
        
        $this->activityUri .= "?from=" . (new \DateTimeImmutable('+12 hours'))->format('Y-m-d H:i:s');
        $this->showAll();
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        $activityResponse = [
            'id' => $this->activityOne->id,
        ];
        $this->seeJsonContains($activityResponse);
    }
    public function test_showAll_filterTo()
    {
        $this->activityOne->startDateTime = (new \DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s');
        $this->activityOne->endDateTime = (new \DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s');
        $this->activityTwo->startDateTime = (new \DateTimeImmutable('-24 hours'))->format('Y-m-d H:i:s');
        $this->activityTwo->endDateTime = (new \DateTimeImmutable('-23 hours'))->format('Y-m-d H:i:s');
        
        $this->activityUri .= "?to=" . (new \DateTimeImmutable('+12 hours'))->format('Y-m-d H:i:s');
        $this->showAll();
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        $activityResponse = [
            'id' => $this->activityTwo->id,
        ];
        $this->seeJsonContains($activityResponse);
    }
    public function test_showAll_filterActivityTypeIdList()
    {
        $this->activityUri .= "?activityTypeIdList[]={$this->activityOne->activityType->id}";
        $this->showAll();
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        $activityResponse = [
            'id' => $this->activityOne->id,
        ];
        $this->seeJsonContains($activityResponse);
    }
    public function test_showAll_filterCancelledStatus()
    {
        $this->activityTwo->cancelled = true;
        
        $this->activityUri .= "?cancelledStatus=false";
        $this->showAll();
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        $activityResponse = [
            'id' => $this->activityOne->id,
        ];
        $this->seeJsonContains($activityResponse);
    }
    public function test_showAll_setOrder()
    {
        $this->activityOne->startDateTime = (new \DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s');
        $this->activityOne->endDateTime = (new \DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s');
        $this->activityTwo->startDateTime = (new \DateTimeImmutable('-24 hours'))->format('Y-m-d H:i:s');
        $this->activityTwo->endDateTime = (new \DateTimeImmutable('-23 hours'))->format('Y-m-d H:i:s');
        
        $this->activityUri .= "?order=DESC&page=1&pageSize=1";;
        $this->showAll();
        
        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);
        $activityResponse = [
            'id' => $this->activityOne->id,
        ];
        $this->seeJsonContains($activityResponse);
    }
    
}
