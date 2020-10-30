<?php

namespace Tests\Controllers\User;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Participant\MetricAssignment\RecordOfAssignmentField,
    Firm\Program\Participant\RecordOfMetricAssignment,
    Firm\Program\RecordOfMetric,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfProgram,
    RecordOfFirm,
    User\RecordOfUserParticipant
};

class ProgramParticipationControllerTest extends ProgramParticipationTestCase
{
    protected $inactiveProgramParticipation;
    protected $metricAssignment;
    protected $assignmentField;
    protected $assignmentFieldOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connection->table("MetricAssignment")->truncate();
        $this->connection->table("Metric")->truncate();
        $this->connection->table("AssignmentField")->truncate();
        
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
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("MetricAssignment")->truncate();
        $this->connection->table("Metric")->truncate();
        $this->connection->table("AssignmentField")->truncate();
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
}
