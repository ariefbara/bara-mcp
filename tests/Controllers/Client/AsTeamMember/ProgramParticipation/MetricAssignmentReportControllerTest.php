<?php

namespace Tests\Controllers\Client\AsTeamMember\ProgramParticipation;

use Tests\Controllers\ {
    Client\AsTeamMember\ProgramParticipationTestCase,
    RecordPreparation\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\RecordOfAssignmentFieldValue,
    RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfAssignmentField,
    RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfMetricAssignmentReport,
    RecordPreparation\Firm\Program\Participant\RecordOfMetricAssignment,
    RecordPreparation\Firm\Program\RecordOfMetric
};

class MetricAssignmentReportControllerTest extends ProgramParticipationTestCase
{
    protected  $metricAssignmentReportUri;
    protected  $metricAssignmentReport;
    protected  $metricAssignmentReportOne;
    
    protected $assignmentFieldValue_00;
    protected $assignmentFieldValue_01;
    protected $assignmentFieldValue_10;
    protected $assignmentFieldValue_11;
    protected $assignmentFieldValue_12;

    protected $metricAssignment;
    
    protected $assignmentField;
    protected $assignmentFieldOne_removed;
    protected $assignmentFieldTwo;

    protected  $submitInput;
    
    protected  $updateInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->metricAssignmentReportUri = $this->programParticipationUri . "/{$this->programParticipation->id}/metric-assignment-reports";
        
        $this->connection->table("Metric")->truncate();
        $this->connection->table("MetricAssignment")->truncate();
        $this->connection->table("AssignmentField")->truncate();
        $this->connection->table("MetricAssignmentReport")->truncate();
        $this->connection->table("AssignmentFieldValue")->truncate();
        
        $participant = $this->programParticipation->participant;
        $program = $participant->program;
        
        $metric = new RecordOfMetric($program, 0);
        $metricOne = new RecordOfMetric($program, 1);
        $metricTwo = new RecordOfMetric($program, 2);
        $this->connection->table("Metric")->insert($metric->toArrayForDbEntry());
        $this->connection->table("Metric")->insert($metricOne->toArrayForDbEntry());
        $this->connection->table("Metric")->insert($metricTwo->toArrayForDbEntry());
        
        $this->metricAssignment = new RecordOfMetricAssignment($participant, 0);
        $this->connection->table("MetricAssignment")->insert($this->metricAssignment->toArrayForDbEntry());
        
        $this->assignmentField = new RecordOfAssignmentField($this->metricAssignment, $metric, 0);
        $this->assignmentFieldOne_removed = new RecordOfAssignmentField($this->metricAssignment, $metricOne, 1);
        $this->assignmentFieldOne_removed->removed = true;
        $this->assignmentFieldTwo = new RecordOfAssignmentField($this->metricAssignment, $metricTwo, 2);
        $this->connection->table("AssignmentField")->insert($this->assignmentField->toArrayForDbEntry());
        $this->connection->table("AssignmentField")->insert($this->assignmentFieldOne_removed->toArrayForDbEntry());
        $this->connection->table("AssignmentField")->insert($this->assignmentFieldTwo->toArrayForDbEntry());
        
        $this->metricAssignmentReport = new RecordOfMetricAssignmentReport($this->metricAssignment, 0);
        $this->metricAssignmentReportOne = new RecordOfMetricAssignmentReport($this->metricAssignment, 1);
        $this->connection->table("MetricAssignmentReport")->insert($this->metricAssignmentReport->toArrayForDbEntry());
        $this->connection->table("MetricAssignmentReport")->insert($this->metricAssignmentReportOne->toArrayForDbEntry());
        
        $this->assignmentFieldValue_00 = new RecordOfAssignmentFieldValue($this->metricAssignmentReport, $this->assignmentField, "00");
        $this->assignmentFieldValue_01 = new RecordOfAssignmentFieldValue($this->metricAssignmentReport, $this->assignmentFieldOne_removed, "01");
        $this->assignmentFieldValue_10 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne, $this->assignmentField, "10");
        $this->assignmentFieldValue_11 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne, $this->assignmentFieldOne_removed, "11");
        $this->assignmentFieldValue_12 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne, $this->assignmentFieldTwo, "12");
        $this->connection->table("AssignmentFieldValue")->insert($this->assignmentFieldValue_00->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->assignmentFieldValue_01->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->assignmentFieldValue_10->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->assignmentFieldValue_11->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->assignmentFieldValue_12->toArrayForDbEntry());
        
        $this->updateInput = [
            "assignmentFieldValues" => [
                [
                    "assignmentFieldId" => $this->assignmentField->id,
                    "value" => 123.123,
                ],
                [
                    "assignmentFieldId" => $this->assignmentFieldTwo->id,
                    "value" => 987.987,
                ],
            ],
        ];
        $this->submitInput = $this->updateInput;
        $this->submitInput["observeTime"] = (new \DateTimeImmutable("-2 days"))->format("Y-m-d H:i:s");
        $this->submitInput["metricAssignmentId"] = $this->metricAssignment->id;
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Metric")->truncate();
        $this->connection->table("MetricAssignment")->truncate();
        $this->connection->table("AssignmentField")->truncate();
        $this->connection->table("MetricAssignmentReport")->truncate();
        $this->connection->table("AssignmentFieldValue")->truncate();
    }
    
    public function test_submit_201()
    {
        $reportResponse = [
            "observeTime" => $this->submitInput["observeTime"],
            "submitTime" => (new \DateTimeImmutable())->format("Y-m-d H:i:s"),
            "removed" => false,
        ];
        $assignedFieldValueResponse = [
            "value" => $this->submitInput["assignmentFieldValues"][0]['value'],
            "assignmentField" => [
                "id" => $this->submitInput["assignmentFieldValues"][0]["assignmentFieldId"],
                "target" => $this->assignmentField->target,
                "metric" => [
                    "id" => $this->assignmentField->metric->id,
                    "name" => $this->assignmentField->metric->name,
                    "minValue" => $this->assignmentField->metric->minValue,
                    "maxValue" => $this->assignmentField->metric->maxValue,
                ],
            ],
        ];
        $assignedFieldValueOneResponse = [
            "value" => $this->submitInput["assignmentFieldValues"][1]['value'],
            "assignmentField" => [
                "id" => $this->submitInput["assignmentFieldValues"][1]["assignmentFieldId"],
                "target" => $this->assignmentFieldTwo->target,
                "metric" => [
                    "id" => $this->assignmentFieldTwo->metric->id,
                    "name" => $this->assignmentFieldTwo->metric->name,
                    "minValue" => $this->assignmentFieldTwo->metric->minValue,
                    "maxValue" => $this->assignmentFieldTwo->metric->maxValue,
                ],
            ],
        ];
        
        $this->post($this->metricAssignmentReportUri, $this->submitInput, $this->teamMember->client->token)
                ->seeJsonContains($reportResponse)
                ->seeJsonContains($assignedFieldValueResponse)
                ->seeJsonContains($assignedFieldValueOneResponse)
                ->seeStatusCode(201);
        $metricAssignmentReportEntry = [
            "observeTime" => $this->submitInput["observeTime"],
            "submitTime" => (new \DateTimeImmutable())->format("Y-m-d H:i:s"),
            "removed" => false,
        ];
        $this->seeInDatabase("MetricAssignmentReport", $metricAssignmentReportEntry);
        
        $assignedFieldValueEntry = [
            "inputValue" => $this->submitInput["assignmentFieldValues"][0]['value'],
            "AssignmentField_id" => $this->submitInput["assignmentFieldValues"][0]["assignmentFieldId"],
        ];
        $this->seeInDatabase("AssignmentFieldValue", $assignedFieldValueEntry);
        $assignedFieldValueOneEntry = [
            "inputValue" => $this->submitInput["assignmentFieldValues"][1]['value'],
            "AssignmentField_id" => $this->submitInput["assignmentFieldValues"][1]["assignmentFieldId"],
        ];
        $this->seeInDatabase("AssignmentFieldValue", $assignedFieldValueOneEntry);
    }
    
    public function test_update_200()
    {
        $assignedFieldValueResponse = [
            "id" => $this->assignmentFieldValue_00->id,
            "value" => $this->submitInput["assignmentFieldValues"][0]['value'],
            "assignmentField" => [
                "id" => $this->assignmentFieldValue_00->assignmentField->id,
                "target" => $this->assignmentFieldValue_00->assignmentField->target,
                "metric" => [
                    "id" => $this->assignmentFieldValue_00->assignmentField->metric->id,
                    "name" => $this->assignmentFieldValue_00->assignmentField->metric->name,
                    "minValue" => $this->assignmentFieldValue_00->assignmentField->metric->minValue,
                    "maxValue" => $this->assignmentFieldValue_00->assignmentField->metric->maxValue,
                ],
            ],
        ];
        $assignedFieldValueTwoResponse = [
            "value" => $this->submitInput["assignmentFieldValues"][1]['value'],
            "assignmentField" => [
                "id" => $this->submitInput["assignmentFieldValues"][1]["assignmentFieldId"],
                "target" => $this->assignmentFieldTwo->target,
                "metric" => [
                    "id" => $this->assignmentFieldTwo->metric->id,
                    "name" => $this->assignmentFieldTwo->metric->name,
                    "minValue" => $this->assignmentFieldTwo->metric->minValue,
                    "maxValue" => $this->assignmentFieldTwo->metric->maxValue,
                ],
            ],
        ];
        $uri = $this->metricAssignmentReportUri . "/{$this->metricAssignmentReport->id}";
        $this->patch($uri, $this->updateInput, $this->teamMember->client->token)
                ->seeJsonContains($assignedFieldValueResponse)
                ->seeJsonContains($assignedFieldValueTwoResponse)
                ->seeStatusCode(200);
        
        $assignedFieldValueEntry = [
            "id" => $this->assignmentFieldValue_00->id,
            "inputValue" => $this->submitInput["assignmentFieldValues"][0]['value'],
            "AssignmentField_id" => $this->submitInput["assignmentFieldValues"][0]["assignmentFieldId"],
            "removed" => false,
        ];
        $this->seeInDatabase("AssignmentFieldValue", $assignedFieldValueEntry);
        $assignedFieldValueOneEntry = [
            "id" => $this->assignmentFieldValue_01->id,
            "removed" => true,
        ];
        $this->seeInDatabase("AssignmentFieldValue", $assignedFieldValueEntry);
        $assignedFieldValueTwoEntry = [
            "inputValue" => $this->submitInput["assignmentFieldValues"][1]['value'],
            "AssignmentField_id" => $this->submitInput["assignmentFieldValues"][1]["assignmentFieldId"],
            "removed" => false,
        ];
        $this->seeInDatabase("AssignmentFieldValue", $assignedFieldValueTwoEntry);
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->metricAssignmentReport->id,
            "observeTime" => $this->metricAssignmentReport->observationTime,
            "submitTime" => $this->metricAssignmentReport->submitTime,
            "removed" => $this->metricAssignmentReport->removed,
            "assignmentFieldValues" => [
                [
                    "id" => $this->assignmentFieldValue_00->id,
                    "value" => $this->assignmentFieldValue_00->inputValue,
                    "assignmentField" => [
                        "id" => $this->assignmentFieldValue_00->assignmentField->id,
                        "target" => $this->assignmentFieldValue_00->assignmentField->target,
                        "metric" => [
                            "id" => $this->assignmentFieldValue_00->assignmentField->metric->id,
                            "name" => $this->assignmentFieldValue_00->assignmentField->metric->name,
                            "minValue" => $this->assignmentFieldValue_00->assignmentField->metric->minValue,
                            "maxValue" => $this->assignmentFieldValue_00->assignmentField->metric->maxValue,
                        ],
                    ],
                ],
                [
                    "id" => $this->assignmentFieldValue_01->id,
                    "value" => $this->assignmentFieldValue_01->inputValue,
                    "assignmentField" => [
                        "id" => $this->assignmentFieldValue_01->assignmentField->id,
                        "target" => $this->assignmentFieldValue_01->assignmentField->target,
                        "metric" => [
                            "id" => $this->assignmentFieldValue_01->assignmentField->metric->id,
                            "name" => $this->assignmentFieldValue_01->assignmentField->metric->name,
                            "minValue" => $this->assignmentFieldValue_01->assignmentField->metric->minValue,
                            "maxValue" => $this->assignmentFieldValue_01->assignmentField->metric->maxValue,
                        ],
                    ],
                ],
            ],
        ];
        $uri = $this->metricAssignmentReportUri . "/{$this->metricAssignmentReport->id}";
        $this->get($uri, $this->teamMember->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_200()
    {
        $reponse = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->metricAssignmentReport->id,
                    "observeTime" => $this->metricAssignmentReport->observationTime,
                    "submitTime" => $this->metricAssignmentReport->submitTime,
                    "removed" => $this->metricAssignmentReport->removed,
                ],
                [
                    "id" => $this->metricAssignmentReportOne->id,
                    "observeTime" => $this->metricAssignmentReportOne->observationTime,
                    "submitTime" => $this->metricAssignmentReportOne->submitTime,
                    "removed" => $this->metricAssignmentReportOne->removed,
                ],
            ],
        ];
        $this->get($this->metricAssignmentReportUri, $this->teamMember->client->token)
                ->seeJsonContains($reponse)
                ->seeStatusCode(200);
    }
}
