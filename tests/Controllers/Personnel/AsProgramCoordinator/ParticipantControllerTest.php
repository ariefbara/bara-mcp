<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use DateTime;
use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Participant\MetricAssignment\RecordOfAssignmentField,
    Firm\Program\Participant\RecordOfMetricAssignment,
    Firm\Program\RecordOfMetric,
    Firm\Program\RecordOfParticipant,
    RecordOfUser,
    User\RecordOfUserParticipant
};

class ParticipantControllerTest extends ParticipantTestCase
{
    protected $participantOne;
    protected $userParticipantOne;
    protected $metricAssignment;
    protected $metric;
    protected $metricOne;
    protected $metricTwo;
    protected $assignmentField;
    protected $assignmentFieldOne;
    protected $assignMetricInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table("Metric")->truncate();
        $this->connection->table("MetricAssignment")->truncate();
        $this->connection->table("AssignmentField")->truncate();
        
        $program = $this->coordinator->program;
        
        $user = new RecordOfUser(1);
        $this->connection->table('User')->insert($user->toArrayForDbEntry());
        
        $this->participantOne = new RecordOfParticipant($program, 1);
        $this->participantOne->active = false;
        $this->connection->table('Participant')->insert($this->participantOne->toArrayForDbEntry());
        
        $this->userParticipantOne = new RecordOfUserParticipant($user, $this->participantOne);
        $this->connection->table('UserParticipant')->insert($this->userParticipantOne->toArrayForDbEntry());
        
        $this->metric = new RecordOfMetric($program, 0);
        $this->metricOne = new RecordOfMetric($program, 1);
        $this->metricTwo = new RecordOfMetric($program, 2);
        $this->connection->table("Metric")->insert($this->metric->toArrayForDbEntry());
        $this->connection->table("Metric")->insert($this->metricOne->toArrayForDbEntry());
        $this->connection->table("Metric")->insert($this->metricTwo->toArrayForDbEntry());
        
        $this->metricAssignment = new RecordOfMetricAssignment($this->participant, 0);
        $this->connection->table("MetricAssignment")->insert($this->metricAssignment->toArrayForDbEntry());
        
        $this->assignmentField = new RecordOfAssignmentField($this->metricAssignment, $this->metric, 0);
        $this->assignmentFieldOne = new RecordOfAssignmentField($this->metricAssignment, $this->metricOne, 1);
        $this->connection->table("AssignmentField")->insert($this->assignmentField->toArrayForDbEntry());
        $this->connection->table("AssignmentField")->insert($this->assignmentFieldOne->toArrayForDbEntry());
        
        $this->assignMetricInput = [
            "startDate" => (new DateTime("+4 months"))->format("Y-m-d H:i:s"),
            "endDate" => (new DateTime("+6 months"))->format("Y-m-d H:i:s"),
            "assignmentFields" => [
                [
                    "metricId" => $this->metric->id,
                    "target" => 888888,
                ],
                [
                    "metricId" => $this->metricTwo->id,
                    "target" => 222222,
                ],
            ],
        ];
        
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Metric")->truncate();
        $this->connection->table("MetricAssignment")->truncate();
        $this->connection->table("AssignmentField")->truncate();
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->participant->id,
            "enrolledTime" => $this->participant->enrolledTime,
            "active" => $this->participant->active,
            "note" => $this->participant->note,
            "user" => [
                "id" => $this->userParticipant->user->id,
                "name" => $this->userParticipant->user->getFullName(),
            ],
            "client" => null,
            "team" => null,
            "metricAssignment" => [
                "startDate" => (new \DateTime($this->metricAssignment->startDate))->format("Y-m-d"),
                "endDate" => (new \DateTime($this->metricAssignment->endDate))->format("Y-m-d"),
                "assignmentFields" => [
                    [
                        "id" => $this->assignmentField->id,
                        "target" => $this->assignmentField->target,
                        "metric" => [
                            "id" => $this->assignmentField->metric->id,
                            "name" => $this->assignmentField->metric->name,
                        ],
                    ],
                    [
                        "id" => $this->assignmentFieldOne->id,
                        "target" => $this->assignmentFieldOne->target,
                        "metric" => [
                            "id" => $this->assignmentFieldOne->metric->id,
                            "name" => $this->assignmentFieldOne->metric->name,
                        ],
                    ],
                ],
            ],
        ];
        
        $uri = $this->participantUri . "/{$this->participant->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_show_personnelNotProgramCoordinator_403()
    {
        $uri = $this->participantUri . "/{$this->participant->id}";
        $this->get($uri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
        
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 2, 
            "list" => [
                [
                    "id" => $this->participant->id,
                    "enrolledTime" => $this->participant->enrolledTime,
                    "active" => $this->participant->active,
                    "note" => $this->participant->note,
                    "user" => [
                        "id" => $this->userParticipant->user->id,
                        "name" => $this->userParticipant->user->getFullName(),
                    ],
                    "client" => null,
                    "team" => null,
                    "hasMetricAssignment" => true,
                ],
                [
                    "id" => $this->participantOne->id,
                    "enrolledTime" => $this->participantOne->enrolledTime,
                    "active" => $this->participantOne->active,
                    "note" => $this->participantOne->note,
                    "user" => [
                        "id" => $this->userParticipantOne->user->id,
                        "name" => $this->userParticipantOne->user->getFullName(),
                    ],
                    "client" => null,
                    "team" => null,
                    "hasMetricAssignment" => false,
                ],
            ],
        ];
        $this->get($this->participantUri, $this->coordinator->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_activeStatusFilterSet()
    {
        $response = [
            "total" => 1, 
            "list" => [
                [
                    "id" => $this->participant->id,
                    "enrolledTime" => $this->participant->enrolledTime,
                    "active" => $this->participant->active,
                    "note" => $this->participant->note,
                    "user" => [
                        "id" => $this->userParticipant->user->id,
                        "name" => $this->userParticipant->user->getFullName(),
                    ],
                    "client" => null,
                    "team" => null,
                    "hasMetricAssignment" => true,
                ],
            ],
        ];
        
        $uri = $this->participantUri . "?activeStatus=true";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_personnelNotCoordinator_403()
    {
        $this->get($this->participantUri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_assignMetric_200()
    {
        $metricAssignmentResponse = [
            "startDate" => (new \DateTime($this->assignMetricInput["startDate"]))->format("Y-m-d"),
            "endDate" => (new \DateTime($this->assignMetricInput["endDate"]))->format("Y-m-d"),
        ];
        $assignmentFieldResponse = [
            "target" => $this->assignMetricInput["assignmentFields"][0]["target"],
            "metric" => [
                "id" => $this->assignMetricInput["assignmentFields"][0]["metricId"],
                "name" => $this->metric->name,
            ],
        ];
        $assignmentFieldOneResponse = [
            "target" => $this->assignMetricInput["assignmentFields"][1]["target"],
            "metric" => [
                "id" => $this->assignMetricInput["assignmentFields"][1]["metricId"],
                "name" => $this->metricTwo->name,
            ],
        ];
        
        $uri = $this->participantUri . "/{$this->participantOne->id}/assign-metric";
        $this->put($uri, $this->assignMetricInput, $this->coordinator->personnel->token)
                ->seeJsonContains($metricAssignmentResponse)
                ->seeJsonContains($assignmentFieldResponse)
                ->seeJsonContains($assignmentFieldOneResponse)
                ->seeStatusCode(200);
        
        $metricAssignmentEntry = [
            "Participant_id" => $this->participantOne->id,
            "startDate" => (new \DateTime($this->assignMetricInput["startDate"]))->format("Y-m-d"),
            "endDate" => (new \DateTime($this->assignMetricInput["endDate"]))->format("Y-m-d"),
        ];
        $this->seeInDatabase("MetricAssignment", $metricAssignmentEntry);
        
        $assignmentFieldEntry = [
            "target" => $this->assignMetricInput["assignmentFields"][0]["target"],
            "Metric_id" => $this->assignMetricInput["assignmentFields"][0]["metricId"],
        ];
        $this->seeInDatabase("AssignmentField", $assignmentFieldEntry);
        $assignmentFieldOneEntry = [
            "target" => $this->assignMetricInput["assignmentFields"][1]["target"],
            "Metric_id" => $this->assignMetricInput["assignmentFields"][1]["metricId"],
        ];
        $this->seeInDatabase("AssignmentField", $assignmentFieldOneEntry);
    }
}
