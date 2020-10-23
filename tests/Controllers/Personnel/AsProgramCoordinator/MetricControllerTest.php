<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMetric;

class MetricControllerTest extends AsProgramCoordinatorTestCase
{
    protected $metricUri;
    protected $metric;
    protected $metricOne;

    protected function setUp(): void
    {
        parent::setUp();
        $this->metricUri = $this->asProgramCoordinatorUri. "/metrics";
        
        $this->connection->table("Metric")->truncate();
        
        $program = $this->coordinator->program;
        
        $this->metric = new RecordOfMetric($program, 0);
        $this->metricOne = new RecordOfMetric($program, 1);
        $this->connection->table("Metric")->insert($this->metric->toArrayForDbEntry());
        $this->connection->table("Metric")->insert($this->metricOne->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Metric")->truncate();
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
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_inactiveManager_403()
    {
        $uri = $this->metricUri . "/{$this->metric->id}";
        $this->get($uri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->metric->id,
                    "name" => $this->metric->name,
                    "description" => $this->metric->description,
                    "minValue" => $this->metric->minValue,
                    "maxValue" => $this->metric->maxValue,
                    "higherIsBetter" => $this->metric->higherIsBetter,
                ],
                [
                    "id" => $this->metricOne->id,
                    "name" => $this->metricOne->name,
                    "description" => $this->metricOne->description,
                    "minValue" => $this->metricOne->minValue,
                    "maxValue" => $this->metricOne->maxValue,
                    "higherIsBetter" => $this->metricOne->higherIsBetter,
                ],
            ],
        ];
        $this->get($this->metricUri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveManager_403()
    {
        $this->get($this->metricUri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }
}
