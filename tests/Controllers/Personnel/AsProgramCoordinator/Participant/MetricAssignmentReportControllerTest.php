<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator\Participant;

use Tests\Controllers\ {
    Personnel\AsProgramCoordinator\ParticipantTestCase,
    RecordPreparation\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\RecordOfAssignmentFieldValue,
    RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfAssignmentField,
    RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfMetricAssignmentReport,
    RecordPreparation\Firm\Program\Participant\RecordOfMetricAssignment,
    RecordPreparation\Firm\Program\RecordOfMetric,
    RecordPreparation\RecordOfUser,
    RecordPreparation\Shared\RecordOfFileInfo,
    RecordPreparation\User\RecordOfUserFileInfo
};

class MetricAssignmentReportControllerTest extends ParticipantTestCase
{
    protected  $metricAssignmentReportUri;
    protected  $metricAssignmentReport;
    protected  $metricAssignmentReportOne;
    
    protected $fileInfo;
    protected $fileInfoOne;
    
    protected $assignmentFieldValue_00;
    protected $assignmentFieldValue_01;
    protected $assignmentFieldValue_10;
    protected $assignmentFieldValue_11;
    protected $assignmentFieldValue_12;

    protected $metricAssignment;
    
    protected $assignmentField;
    protected $assignmentFieldOne_removed;
    protected $assignmentFieldTwo;
    protected $rejectInput = ["note" => "new note"];

    protected function setUp(): void
    {
        parent::setUp();
        $this->metricAssignmentReportUri = $this->participantUri . "/{$this->participant->id}/metric-assignment-reports";
        
        $this->connection->table("User")->truncate();
        $this->connection->table("FileInfo")->truncate();
        $this->connection->table("UserFileInfo")->truncate();
        $this->connection->table("Metric")->truncate();
        $this->connection->table("MetricAssignment")->truncate();
        $this->connection->table("AssignmentField")->truncate();
        $this->connection->table("MetricAssignmentReport")->truncate();
        $this->connection->table("AssignmentFieldValue")->truncate();
        
        $participant = $this->participant;
        $program = $participant->program;
        $user = new RecordOfUser(0);
        $this->connection->table("User")->insert($user->toArrayForDbEntry());
        
        $this->fileInfo = new RecordOfFileInfo(0);
        $this->fileInfoOne = new RecordOfFileInfo(1);
        $this->connection->table("FileInfo")->insert($this->fileInfo->toArrayForDbEntry());
        $this->connection->table("FileInfo")->insert($this->fileInfoOne->toArrayForDbEntry());
        
        $userFileInfo = new RecordOfUserFileInfo($user, $this->fileInfo);
        $userFileInfoOne = new RecordOfUserFileInfo($user, $this->fileInfoOne);
        $this->connection->table("UserFileInfo")->insert($userFileInfo->toArrayForDbEntry());
        $this->connection->table("UserFileInfo")->insert($userFileInfoOne->toArrayForDbEntry());
        
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
        $this->assignmentFieldOne_removed->disabled = true;
        $this->assignmentFieldTwo = new RecordOfAssignmentField($this->metricAssignment, $metricTwo, 2);
        $this->connection->table("AssignmentField")->insert($this->assignmentField->toArrayForDbEntry());
        $this->connection->table("AssignmentField")->insert($this->assignmentFieldOne_removed->toArrayForDbEntry());
        $this->connection->table("AssignmentField")->insert($this->assignmentFieldTwo->toArrayForDbEntry());
        
        $this->metricAssignmentReport = new RecordOfMetricAssignmentReport($this->metricAssignment, 0);
        $this->metricAssignmentReportOne = new RecordOfMetricAssignmentReport($this->metricAssignment, 1);
        $this->connection->table("MetricAssignmentReport")->insert($this->metricAssignmentReport->toArrayForDbEntry());
        $this->connection->table("MetricAssignmentReport")->insert($this->metricAssignmentReportOne->toArrayForDbEntry());
        
        $this->assignmentFieldValue_00 = new RecordOfAssignmentFieldValue($this->metricAssignmentReport, $this->assignmentField, "00", $this->fileInfoOne);
        $this->assignmentFieldValue_01 = new RecordOfAssignmentFieldValue($this->metricAssignmentReport, $this->assignmentFieldOne_removed, "01");
        $this->assignmentFieldValue_10 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne, $this->assignmentField, "10");
        $this->assignmentFieldValue_11 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne, $this->assignmentFieldOne_removed, "11");
        $this->assignmentFieldValue_12 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne, $this->assignmentFieldTwo, "12");
        $this->connection->table("AssignmentFieldValue")->insert($this->assignmentFieldValue_00->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->assignmentFieldValue_01->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->assignmentFieldValue_10->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->assignmentFieldValue_11->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->assignmentFieldValue_12->toArrayForDbEntry());
        
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
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->metricAssignmentReport->id,
            "observationTime" => $this->metricAssignmentReport->observationTime,
            "submitTime" => $this->metricAssignmentReport->submitTime,
            "removed" => $this->metricAssignmentReport->removed,
            "approved" => $this->metricAssignmentReport->approved,
            "note" => $this->metricAssignmentReport->note,
            "assignmentFieldValues" => [
                [
                    "id" => $this->assignmentFieldValue_00->id,
                    "value" => $this->assignmentFieldValue_00->inputValue,
                    "note" => $this->assignmentFieldValue_00->note,
                    "fileInfo" => [
                        "id" => $this->assignmentFieldValue_00->attachedFileInfo->id,
                        "path" => DIRECTORY_SEPARATOR . $this->assignmentFieldValue_00->attachedFileInfo->name,
                    ],
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
                    "note" => $this->assignmentFieldValue_01->note,
                    "fileInfo" => null,
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
        $this->get($uri, $this->coordinator->personnel->token)
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
                    "observationTime" => $this->metricAssignmentReport->observationTime,
                    "submitTime" => $this->metricAssignmentReport->submitTime,
                    "approved" => $this->metricAssignmentReport->approved,
                    "note" => $this->metricAssignmentReport->note,
                    "removed" => $this->metricAssignmentReport->removed,
                    "assignmentFieldValues" => [
                        [
                            "id" => $this->assignmentFieldValue_00->id,
                            "value" => $this->assignmentFieldValue_00->inputValue,
                            "note" => $this->assignmentFieldValue_00->note,
                            "fileInfo" => [
                                "id" => $this->assignmentFieldValue_00->attachedFileInfo->id,
                                "path" => DIRECTORY_SEPARATOR . $this->assignmentFieldValue_00->attachedFileInfo->name,
                            ],
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
                            "note" => $this->assignmentFieldValue_01->note,
                            "fileInfo" => null,
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
                ],
                [
                    "id" => $this->metricAssignmentReportOne->id,
                    "observationTime" => $this->metricAssignmentReportOne->observationTime,
                    "submitTime" => $this->metricAssignmentReportOne->submitTime,
                    "approved" => $this->metricAssignmentReportOne->approved,
                    "note" => $this->metricAssignmentReportOne->note,
                    "removed" => $this->metricAssignmentReportOne->removed,
                    "assignmentFieldValues" => [
                        [
                            "id" => $this->assignmentFieldValue_10->id,
                            "value" => $this->assignmentFieldValue_10->inputValue,
                            "note" => $this->assignmentFieldValue_10->note,
                            "fileInfo" => null,
                            "assignmentField" => [
                                "id" => $this->assignmentFieldValue_10->assignmentField->id,
                                "target" => $this->assignmentFieldValue_10->assignmentField->target,
                                "metric" => [
                                    "id" => $this->assignmentFieldValue_10->assignmentField->metric->id,
                                    "name" => $this->assignmentFieldValue_10->assignmentField->metric->name,
                                    "minValue" => $this->assignmentFieldValue_10->assignmentField->metric->minValue,
                                    "maxValue" => $this->assignmentFieldValue_10->assignmentField->metric->maxValue,
                                ],
                            ],
                        ],
                        [
                            "id" => $this->assignmentFieldValue_11->id,
                            "value" => $this->assignmentFieldValue_11->inputValue,
                            "note" => $this->assignmentFieldValue_11->note,
                            "fileInfo" => null,
                            "assignmentField" => [
                                "id" => $this->assignmentFieldValue_11->assignmentField->id,
                                "target" => $this->assignmentFieldValue_11->assignmentField->target,
                                "metric" => [
                                    "id" => $this->assignmentFieldValue_11->assignmentField->metric->id,
                                    "name" => $this->assignmentFieldValue_11->assignmentField->metric->name,
                                    "minValue" => $this->assignmentFieldValue_11->assignmentField->metric->minValue,
                                    "maxValue" => $this->assignmentFieldValue_11->assignmentField->metric->maxValue,
                                ],
                            ],
                        ],
                        [
                            "id" => $this->assignmentFieldValue_12->id,
                            "value" => $this->assignmentFieldValue_12->inputValue,
                            "note" => $this->assignmentFieldValue_12->note,
                            "fileInfo" => null,
                            "assignmentField" => [
                                "id" => $this->assignmentFieldValue_12->assignmentField->id,
                                "target" => $this->assignmentFieldValue_12->assignmentField->target,
                                "metric" => [
                                    "id" => $this->assignmentFieldValue_12->assignmentField->metric->id,
                                    "name" => $this->assignmentFieldValue_12->assignmentField->metric->name,
                                    "minValue" => $this->assignmentFieldValue_12->assignmentField->metric->minValue,
                                    "maxValue" => $this->assignmentFieldValue_12->assignmentField->metric->maxValue,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->get($this->metricAssignmentReportUri, $this->coordinator->personnel->token)
                ->seeJsonContains($reponse)
                ->seeStatusCode(200);
    }
    
    public function test_approve_200()
    {
        $response = [
            "id" => $this->metricAssignmentReport->id,
            "observationTime" => $this->metricAssignmentReport->observationTime,
            "submitTime" => $this->metricAssignmentReport->submitTime,
            "approved" => true,
            "removed" => $this->metricAssignmentReport->removed,
        ];
        
        $uri = $this->metricAssignmentReportUri . "/{$this->metricAssignmentReport->id}/approve";
        $this->patch($uri, [], $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $metricAssignmentReportEntry = [
            "id" => $this->metricAssignmentReport->id,
            "approved" => true,
        ];
        $this->seeInDatabase("MetricAssignmentReport", $metricAssignmentReportEntry);
    }
    
    public function test_reject_200()
    {
        $response = [
            "id" => $this->metricAssignmentReport->id,
            "observationTime" => $this->metricAssignmentReport->observationTime,
            "submitTime" => $this->metricAssignmentReport->submitTime,
            "approved" => false,
            "note" => $this->rejectInput["note"],
            "removed" => $this->metricAssignmentReport->removed,
        ];
        
        $uri = $this->metricAssignmentReportUri . "/{$this->metricAssignmentReport->id}/reject";
        $this->patch($uri, $this->rejectInput, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $metricAssignmentReportEntry = [
            "id" => $this->metricAssignmentReport->id,
            "approved" => false,
            "note" => $this->rejectInput["note"],
        ];
        $this->seeInDatabase("MetricAssignmentReport", $metricAssignmentReportEntry);
    }
}
