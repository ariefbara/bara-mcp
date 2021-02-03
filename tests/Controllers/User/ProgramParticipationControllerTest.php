<?php

namespace Tests\Controllers\User;

use DateTime;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\RecordOfAssignmentFieldValue;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfAssignmentField;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfMetricAssignmentReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMetricAssignment;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMetric;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class ProgramParticipationControllerTest extends ProgramParticipationTestCase
{
    protected $inactiveProgramParticipation;
    protected $metricAssignment;
    protected $assignmentField;
    protected $assignmentFieldOne;
    protected $metricAssignmentReportOne_lastApproved;
    protected $metricAssignmentReportTwo_last;
    protected $assignmentFieldValue_00;
    protected $assignmentFieldValue_01;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connection->table("MetricAssignment")->truncate();
        $this->connection->table("Metric")->truncate();
        $this->connection->table("AssignmentField")->truncate();
        $this->connection->table("MetricAssignmentReport")->truncate();
        $this->connection->table("AssignmentFieldValue")->truncate();
        
        $firm = new RecordOfFirm(1, 'firm-1-identifier');
        $this->connection->table('Firm')->insert($firm->toArrayForDbEntry());
        
        $program = new RecordOfProgram($firm, 1);
        $this->connection->table('Program')->insert($program->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($program, 1);
        $participant->active = false;
        $participant->note = 'quit';
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());
        
        $this->inactiveProgramParticipation = new RecordOfUserParticipant($this->user, $participant);
        $this->connection->table('UserParticipant')->insert($this->inactiveProgramParticipation->toArrayForDbEntry());
        
        $this->metricAssignment = new RecordOfMetricAssignment($this->programParticipation->participant, 0);
        $this->connection->table("MetricAssignment")->insert($this->metricAssignment->toArrayForDbEntry());
        
        $metric = new RecordOfMetric($program, 0);
        $metricOne = new RecordOfMetric($program, 1);
        $this->connection->table("Metric")->insert($metric->toArrayForDbEntry());
        $this->connection->table("Metric")->insert($metricOne->toArrayForDbEntry());
        
        $this->assignmentField = new RecordOfAssignmentField($this->metricAssignment, $metric, 0);
        $this->assignmentFieldOne = new RecordOfAssignmentField($this->metricAssignment, $metricOne, 1);
        $this->connection->table("AssignmentField")->insert($this->assignmentField->toArrayForDbEntry());
        $this->connection->table("AssignmentField")->insert($this->assignmentFieldOne->toArrayForDbEntry());
        
        $this->metricAssignmentReport = new RecordOfMetricAssignmentReport($this->metricAssignment, 0);
        $this->metricAssignmentReport->observationTime = (new DateTime("-2 months"))->format("Y-m-d H:i:s");
        $this->metricAssignmentReport->approved = true;
        $this->metricAssignmentReportOne_lastApproved = new RecordOfMetricAssignmentReport($this->metricAssignment, 1);
        $this->metricAssignmentReportOne_lastApproved->observationTime = (new DateTime("-2 weeks"))->format("Y-m-d H:i:s");
        $this->metricAssignmentReportOne_lastApproved->approved = true;
        $this->metricAssignmentReportTwo_last = new RecordOfMetricAssignmentReport($this->metricAssignment, 2);
        $this->metricAssignmentReportTwo_last->observationTime = (new DateTime("-2 days"))->format("Y-m-d H:i:s");
        $this->connection->table("MetricAssignmentReport")->insert($this->metricAssignmentReport->toArrayForDbEntry());
        $this->connection->table("MetricAssignmentReport")->insert($this->metricAssignmentReportOne_lastApproved->toArrayForDbEntry());
        $this->connection->table("MetricAssignmentReport")->insert($this->metricAssignmentReportTwo_last->toArrayForDbEntry());
        
        $this->assignmentFieldValue_00 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne_lastApproved, $this->assignmentField, "00");
        $this->assignmentFieldValue_01 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne_lastApproved, $this->assignmentFieldOne, "01");
        $this->connection->table("AssignmentFieldValue")->insert($this->assignmentFieldValue_00->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->assignmentFieldValue_01->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("MetricAssignment")->truncate();
        $this->connection->table("Metric")->truncate();
        $this->connection->table("AssignmentField")->truncate();
        $this->connection->table("MetricAssignmentReport")->truncate();
        $this->connection->table("AssignmentFieldValue")->truncate();
    }
    
    public function test_quit_200()
    {
        $uri = $this->programParticipationUri . "/{$this->programParticipation->id}/quit";
        $this->patch($uri, [], $this->user->token)
                ->seeStatusCode(200);
        
        $participantEntry = [
            'id' => $this->programParticipation->participant->id,
            'active' => false,
            'note' => 'quit',
        ];
        $this->seeInDatabase('Participant', $participantEntry);
    }
    public function test_quit_alreadyInactive_403()
    {
        $uri = $this->programParticipationUri . "/{$this->inactiveProgramParticipation->id}/quit";
        $this->patch($uri, [], $this->user->token)
                ->seeStatusCode(403);
        
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->programParticipation->id,
            'program' => [
                'id' => $this->programParticipation->participant->program->id,
                'name' => $this->programParticipation->participant->program->name,
                'removed' => $this->programParticipation->participant->program->removed,
                'firm' => [
                    'id' => $this->programParticipation->participant->program->firm->id,
                    'name' => $this->programParticipation->participant->program->firm->name,
                ],
            ],
            'enrolledTime' => $this->programParticipation->participant->enrolledTime,
            'active' => $this->programParticipation->participant->active,
            'note' => $this->programParticipation->participant->note,
            "metricAssignment" => [
                "id" => $this->metricAssignment->id,
                "startDate" => $this->metricAssignment->startDate,
                "endDate" => $this->metricAssignment->endDate,
                "assignmentFields" => [
                    [
                        "id" => $this->assignmentField->id,
                        "target" => $this->assignmentField->target,
                        "metric" => [
                            "id" => $this->assignmentField->metric->id,
                            "name" => $this->assignmentField->metric->name,
                            "minValue" => $this->assignmentField->metric->minValue,
                            "maxValue" => $this->assignmentField->metric->maxValue,
                            "higherIsBetter" => $this->assignmentField->metric->higherIsBetter,
                        ],
                    ],
                    [
                        "id" => $this->assignmentFieldOne->id,
                        "target" => $this->assignmentFieldOne->target,
                        "metric" => [
                            "id" => $this->assignmentFieldOne->metric->id,
                            "name" => $this->assignmentFieldOne->metric->name,
                            "minValue" => $this->assignmentFieldOne->metric->minValue,
                            "maxValue" => $this->assignmentFieldOne->metric->maxValue,
                            "higherIsBetter" => $this->assignmentFieldOne->metric->higherIsBetter,
                        ],
                    ],
                ],
                "lastMetricAssignmentReport" => [
                    "id" => $this->metricAssignmentReportOne_lastApproved->id,
                    "observationTime" => $this->metricAssignmentReportOne_lastApproved->observationTime,
                    "submitTime" => $this->metricAssignmentReportOne_lastApproved->submitTime,
                    "removed" => $this->metricAssignmentReportOne_lastApproved->removed,
                    "assignmentFieldValues" => [
                        [
                            "id" => $this->assignmentFieldValue_00->id,
                            "value" => $this->assignmentFieldValue_00->inputValue,
                            "assignmentFieldId" => $this->assignmentFieldValue_00->assignmentField->id,
                        ],
                        [
                            "id" => $this->assignmentFieldValue_01->id,
                            "value" => $this->assignmentFieldValue_01->inputValue,
                            "assignmentFieldId" => $this->assignmentFieldValue_01->assignmentField->id,
                        ],
                    ],
                ],
            ],
        ];
        
        $uri = $this->programParticipationUri . "/{$this->programParticipation->id}";
        $this->get($uri, $this->user->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    
    public function test_showAll_200()
    {
        $response = [
            'total' => 2,
            'list' => [
                [
                    "id" => $this->programParticipation->id,
                    'program' => [
                        'id' => $this->programParticipation->participant->program->id,
                        'name' => $this->programParticipation->participant->program->name,
                        'removed' => $this->programParticipation->participant->program->removed,
                        'firm' => [
                            'id' => $this->programParticipation->participant->program->firm->id,
                            'name' => $this->programParticipation->participant->program->firm->name,
                        ],
                    ],
                    'enrolledTime' => $this->programParticipation->participant->enrolledTime,
                    'active' => $this->programParticipation->participant->active,
                    'note' => $this->programParticipation->participant->note,
                ],
                [
                    "id" => $this->inactiveProgramParticipation->id,
                    'program' => [
                        'id' => $this->inactiveProgramParticipation->participant->program->id,
                        'name' => $this->inactiveProgramParticipation->participant->program->name,
                        'removed' => $this->inactiveProgramParticipation->participant->program->removed,
                        'firm' => [
                            'id' => $this->inactiveProgramParticipation->participant->program->firm->id,
                            'name' => $this->inactiveProgramParticipation->participant->program->firm->name,
                        ],
                    ],
                    'enrolledTime' => $this->inactiveProgramParticipation->participant->enrolledTime,
                    'active' => $this->inactiveProgramParticipation->participant->active,
                    'note' => $this->inactiveProgramParticipation->participant->note,
                ],
            ],
        ];
        
        $this->get($this->programParticipationUri, $this->user->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_applyActiveStatusFilter_200()
    {
        $totalResponse = ["total" => 1];
        $programParticipationResponse = ["id" => $this->programParticipation->id];
        $uri = $this->programParticipationUri . "?activeStatus=true";
        $this->get($uri, $this->user->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($programParticipationResponse)
                ->seeStatusCode(200);
    }
}
