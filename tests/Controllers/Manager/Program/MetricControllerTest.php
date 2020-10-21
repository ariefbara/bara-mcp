<?php

namespace Tests\Controllers\Manager\Program;

use Tests\Controllers\ {
    Manager\ProgramTestCase,
    RecordPreparation\Firm\Program\RecordOfMetric
};

class MetricControllerTest extends ProgramTestCase
{
    protected $metricUri;
    protected $metric;
    protected $metricOne;
    protected $dataInput = [
        "name" => "new metric name",
        "description" => "new metric description",
        "minValue" => 222,
        "maxValue" => 888888,
        "higherIsBetter" => true,
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->metricUri = $this->programUri . "/{$this->program->id}/metrics";
        
        $this->connection->table("Metric")->truncate();
        
        $this->metric = new RecordOfMetric($this->program, 0);
        $this->metricOne = new RecordOfMetric($this->program, 1);
        $this->connection->table("Metric")->insert($this->metric->toArrayForDbEntry());
        $this->connection->table("Metric")->insert($this->metricOne->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Metric")->truncate();
    }
    
    public function test_add_201()
    {
        $response = [
            "name" => $this->dataInput["name"],
            "description" => $this->dataInput["description"],
            "minValue" => $this->dataInput["minValue"],
            "maxValue" => $this->dataInput["maxValue"],
            "higherIsBetter" => $this->dataInput["higherIsBetter"],
        ];
        
        $this->post($this->metricUri, $this->dataInput, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(201);
        
        $metricEntry = [
            "Program_id" => $this->program->id,
            "name" => $this->dataInput["name"],
            "description" => $this->dataInput["description"],
            "minimumValue" => $this->dataInput["minValue"],
            "maximumValue" => $this->dataInput["maxValue"],
            "higherIsBetter" => $this->dataInput["higherIsBetter"],
        ];
        $this->seeInDatabase("Metric", $metricEntry);
    }
    public function test_add_emptyName_400()
    {
        $this->dataInput["name"] = "";
        $this->post($this->metricUri, $this->dataInput, $this->manager->token)
                ->seeStatusCode(400);
    }
    public function test_add_inactiveManager_401()
    {
        $this->post($this->metricUri, $this->dataInput, $this->removedManager->token)
                ->seeStatusCode(401);
    }
    
    public function test_update_200()
    {
        $response = [
            "id" => $this->metric->id,
            "name" => $this->dataInput["name"],
            "description" => $this->dataInput["description"],
            "minValue" => $this->dataInput["minValue"],
            "maxValue" => $this->dataInput["maxValue"],
            "higherIsBetter" => $this->dataInput["higherIsBetter"],
        ];
        
        $uri = $this->metricUri . "/{$this->metric->id}";
        $this->patch($uri, $this->dataInput, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $metricEntry = [
            "id" => $this->metric->id,
            "Program_id" => $this->program->id,
            "name" => $this->dataInput["name"],
            "description" => $this->dataInput["description"],
            "minimumValue" => $this->dataInput["minValue"],
            "maximumValue" => $this->dataInput["maxValue"],
            "higherIsBetter" => $this->dataInput["higherIsBetter"],
        ];
        $this->seeInDatabase("Metric", $metricEntry);
    }
    public function test_update_emptyName_400()
    {
        $this->dataInput["name"] = "";
        $uri = $this->metricUri . "/{$this->metric->id}";
        $this->patch($uri, $this->dataInput, $this->manager->token)
                ->seeStatusCode(400);
    }
    public function test_update_inactiveManager_401()
    {
        $uri = $this->metricUri . "/{$this->metric->id}";
        $this->patch($uri, $this->dataInput, $this->removedManager->token)
                ->seeStatusCode(401);
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->metric->id,
            "name" => $this->metric->name,
            "description" => $this->metric->description,
            "minValue" => $this->metric->minValue,
            "maxValue" => $this->metric->maxValue,
            "higherIsBetter" => $this->metric->higherIsBetter,
        ];
        
        $uri = $this->metricUri . "/{$this->metric->id}";
        $this->get($uri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_inactiveManager_401()
    {
        $uri = $this->metricUri . "/{$this->metric->id}";
        $this->get($uri, $this->removedManager->token)
                ->seeStatusCode(401);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->metric->id,
                    "name" => $this->metric->name,
                ],
                [
                    "id" => $this->metricOne->id,
                    "name" => $this->metricOne->name,
                ],
            ],
        ];
        $this->get($this->metricUri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveManager_401()
    {
        $this->get($this->metricUri, $this->removedManager->token)
                ->seeStatusCode(401);
    }
}
