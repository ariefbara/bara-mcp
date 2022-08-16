<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use DateTime;
use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\RecordOfAssignmentFieldValue;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfAssignmentField;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfMetricAssignmentReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMetricAssignment;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfEvaluationPlan;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMetric;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\RecordOfUser;

class ParticipantControllerTest extends ParticipantTestCase
{

    protected $participantOne_client;
    protected $participantTwo_team;
    protected $clientParticipant;
    protected $teamParticipant;
    protected $memberOne;
    protected $memberTwo;
    protected $metricAssignment;
    protected $metric;
    protected $metricOne;
    protected $metricTwo;
    protected $assignmentField;
    protected $assignmentFieldOne;
    protected $assignMetricInput;
    protected $metricAssignmentReport;
    protected $metricAssignmentReportOne_lastApproved;
    protected $metricAssignmentReportTwo_last;
    protected $assignmentFieldValue_00;
    protected $assignmentFieldValue_01;
    protected $evaluationPlan;
    protected $evaluateInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table("Metric")->truncate();
        $this->connection->table("MetricAssignment")->truncate();
        $this->connection->table("AssignmentField")->truncate();
        $this->connection->table("MetricAssignmentReport")->truncate();
        $this->connection->table("AssignmentFieldValue")->truncate();
        $this->connection->table("EvaluationPlan")->truncate();
        $this->connection->table("Evaluation")->truncate();

        $program = $this->coordinator->program;
        $firm = $program->firm;

        $user = new RecordOfUser(1);
        $this->connection->table('User')->insert($user->toArrayForDbEntry());

        $this->participantOne_client = new RecordOfParticipant($program, 1);
        $this->participantOne_client->active = false;
        $this->participantTwo_team = new RecordOfParticipant($program, 2);
        $this->connection->table('Participant')->insert($this->participantOne_client->toArrayForDbEntry());
        $this->connection->table('Participant')->insert($this->participantTwo_team->toArrayForDbEntry());

        $client = new RecordOfClient($firm, '0');
        $clientOne = new RecordOfClient($firm, '1');
        $clientTwo = new RecordOfClient($firm, '2');
        $this->connection->table('Client')->insert($client->toArrayForDbEntry());

        $this->clientParticipant = new RecordOfClientParticipant($client, $this->participantOne_client);
        $this->connection->table('ClientParticipant')->insert($this->clientParticipant->toArrayForDbEntry());

        $team = new RecordOfTeam($firm, $client, 0);
        $this->connection->table('Team')->insert($team->toArrayForDbEntry());

        $this->memberOne = new RecordOfMember($team, $clientOne, '1');
        $this->memberTwo = new RecordOfMember($team, $clientTwo, '2');

        $this->teamParticipant = new RecordOfTeamProgramParticipation($team, $this->participantTwo_team);
        $this->connection->table('TeamParticipant')->insert($this->teamParticipant->toArrayForDbEntry());

        $this->metric = new RecordOfMetric($program, 0);
        $this->metricOne = new RecordOfMetric($program, 1);
        $this->metricTwo = new RecordOfMetric($program, 2);
        $this->connection->table("Metric")->insert($this->metric->toArrayForDbEntry());
        $this->connection->table("Metric")->insert($this->metricOne->toArrayForDbEntry());
        $this->connection->table("Metric")->insert($this->metricTwo->toArrayForDbEntry());

        $this->metricAssignment = new RecordOfMetricAssignment($this->teamParticipant->participant, 0);
        $this->connection->table("MetricAssignment")->insert($this->metricAssignment->toArrayForDbEntry());

        $this->assignmentField = new RecordOfAssignmentField($this->metricAssignment, $this->metric, 0);
        $this->assignmentFieldOne = new RecordOfAssignmentField($this->metricAssignment, $this->metricOne, 1);
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

        $this->assignmentFieldValue_00 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne_lastApproved,
                $this->assignmentField, "00");
        $this->assignmentFieldValue_01 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne_lastApproved,
                $this->assignmentFieldOne, "01");
        $this->connection->table("AssignmentFieldValue")->insert($this->assignmentFieldValue_00->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->assignmentFieldValue_01->toArrayForDbEntry());

        $this->evaluationPlan = new RecordOfEvaluationPlan($program, null, 0);
        $this->connection->table("EvaluationPlan")->insert($this->evaluationPlan->toArrayForDbEntry());

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

        $this->evaluateInput = [
            "evaluationPlanId" => $this->evaluationPlan->id,
            "status" => "pass",
            "extendDays" => null,
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Metric")->truncate();
        $this->connection->table("MetricAssignment")->truncate();
        $this->connection->table("AssignmentField")->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table("MetricAssignmentReport")->truncate();
        $this->connection->table("AssignmentFieldValue")->truncate();
        $this->connection->table("EvaluationPlan")->truncate();
        $this->connection->table("Evaluation")->truncate();
    }

    protected function show()
    {
        $this->memberOne->client->insert($this->connection);
        $this->memberTwo->client->insert($this->connection);

        $this->memberOne->insert($this->connection);
        $this->memberTwo->insert($this->connection);

        $uri = $this->participantUri . "/{$this->teamParticipant->participant->id}";
        $this->get($uri, $this->coordinator->personnel->token);
echo $uri;
    }
    public function test_show()
    {
        $this->connection->table("MetricAssignmentReport")->truncate();

        $this->show();
        $this->seeStatusCode(200);

        $response = [
            "id" => $this->teamParticipant->participant->id,
            "enrolledTime" => $this->teamParticipant->participant->enrolledTime,
            "active" => $this->teamParticipant->participant->active,
            "note" => $this->teamParticipant->participant->note,
            "user" => null,
            "client" => null,
            "team" => [
                'id' => $this->teamParticipant->team->id,
                'name' => $this->teamParticipant->team->name,
                'members' => [
                    [
                        'id' => $this->memberOne->id,
                        'client' => [
                            'id' => $this->memberOne->client->id,
                            'name' => $this->memberOne->client->getFullName(),
                        ],
                    ],
                    [
                        'id' => $this->memberTwo->id,
                        'client' => [
                            'id' => $this->memberTwo->client->id,
                            'name' => $this->memberTwo->client->getFullName(),
                        ],
                    ],
                ],
            ],
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
                "lastMetricAssignmentReport" => null,
            ],
            "lastEvaluation" => null,
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_personnelNotProgramCoordinator_403()
    {
        $uri = $this->participantUri . "/{$this->participant->id}";
        $this->get($uri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }
    public function test_show_hasMetricAssignmentReport_includeLastApprovedReportInResponse()
    {
        $this->show();
        $this->seeStatusCode(200);
        $response = [
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
        ];
        $this->seeJsonContains($response);
    }

    protected function showAll()
    {
        $this->memberOne->client->insert($this->connection);
        $this->memberTwo->client->insert($this->connection);

        $this->memberOne->insert($this->connection);
        $this->memberTwo->insert($this->connection);

        $this->get($this->participantUri, $this->coordinator->personnel->token);
    }
    public function test_showAll()
    {
        $this->showAll();
        $this->seeStatusCode(200);

        $totalResponse = ['total' => 3];
        $this->seeJsonContains($totalResponse);
        
        $teamParticipantReponse = [
            "id" => $this->teamParticipant->participant->id,
            "enrolledTime" => $this->teamParticipant->participant->enrolledTime,
            "active" => $this->teamParticipant->participant->active,
            "note" => $this->teamParticipant->participant->note,
            "user" => null,
            "client" => null,
            "team" => [
                "id" => $this->teamParticipant->team->id,
                "name" => $this->teamParticipant->team->name,
                'members' => [
                    [
                        'id' => $this->memberOne->id,
                        'client' => [
                            'id' => $this->memberOne->client->id,
                            'name' => $this->memberOne->client->getFullName(),
                        ],
                    ],
                    [
                        'id' => $this->memberTwo->id,
                        'client' => [
                            'id' => $this->memberTwo->client->id,
                            'name' => $this->memberTwo->client->getFullName(),
                        ],
                    ],
                ],
            ],
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
            "lastEvaluation" => null,
        ];
        $this->seeJsonContains($teamParticipantReponse);
        
        $clientParticipantResponse = [
                    "id" => $this->clientParticipant->participant->id,
                    "enrolledTime" => $this->clientParticipant->participant->enrolledTime,
                    "active" => $this->clientParticipant->participant->active,
                    "note" => $this->clientParticipant->participant->note,
                    "user" => null,
                    "client" => [
                        "id" => $this->clientParticipant->client->id,
                        "name" => $this->clientParticipant->client->getFullName(),
                    ],
                    "team" => null,
                    "metricAssignment" => null,
                    "lastEvaluation" => null,
                ];
        $this->seeJsonContains($clientParticipantResponse);
        
        $userParticipantResponse = [
                    "id" => $this->userParticipant->participant->id,
                    "enrolledTime" => $this->userParticipant->participant->enrolledTime,
                    "active" => $this->userParticipant->participant->active,
                    "note" => $this->userParticipant->participant->note,
                    "user" => [
                        "id" => $this->userParticipant->user->id,
                        "name" => $this->userParticipant->user->getFullName(),
                    ],
                    "client" => null,
                    "team" => null,
                    "metricAssignment" => null,
                    "lastEvaluation" => null,
                ];
        $this->seeJsonContains($userParticipantResponse);
    }
    public function test_showAll_activeStatusFilterSet()
    {
        $this->participantUri .= "?activeStatus=true";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = [
            'total' => 2,
        ];
        $this->seeJsonContains($totalResponse);
        
        $userParticipantResponse = [
            'id' => $this->userParticipant->participant->id,
        ];
        $this->seeJsonContains($userParticipantResponse);
        
        $teamParticipantResponse = [
            'id' => $this->teamParticipant->participant->id,
        ];
        $this->seeJsonContains($teamParticipantResponse);
    }
    public function test_showAll_personnelNotCoordinator_403()
    {
        $this->get($this->participantUri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }
    public function test_showAll_failFilterSet()
    {
        $this->participantOne_client->note = "fail";
        $this->participantOne_client->active = false;
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Participant")->insert($this->participantOne_client->toArrayForDbEntry());

        $this->participantUri .= '?note=fail';
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ["total" => 1];
        $this->seeJsonContains($totalResponse);
        
        $participantOneResponse = [
            "id" => $this->participantOne_client->id,
        ];
        $this->seeJsonContains($participantOneResponse);
    }
    public function test_showAll_completedFilterSet()
    {
        $this->participantTwo_team->note = "completed";
        $this->participantTwo_team->active = false;
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Participant")->insert($this->participantTwo_team->toArrayForDbEntry());
        
        $this->participantUri .= '?note=completed';
        
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ["total" => 1];
        $this->seeJsonContains($totalResponse);
        
        $participantTwoResponse = [
            "id" => $this->participantTwo_team->id,
        ];
        $this->seeJsonContains($participantTwoResponse);
    }
    public function test_showAll_searchByName()
    {
        $uri = $this->participantUri . "?searchByName=user";
        $totalResponse = ["total" => 1];
        $participantOneResponse = [
            "id" => $this->participant->id,
        ];
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($participantOneResponse);
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

        $uri = $this->participantUri . "/{$this->participantOne_client->id}/assign-metric";
        $this->put($uri, $this->assignMetricInput, $this->coordinator->personnel->token)
                ->seeJsonContains($metricAssignmentResponse)
                ->seeJsonContains($assignmentFieldResponse)
                ->seeJsonContains($assignmentFieldOneResponse)
                ->seeStatusCode(200);

        $metricAssignmentEntry = [
            "Participant_id" => $this->participantOne_client->id,
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
    public function test_assignMetric_alreadyHasMetricAssignment_update()
    {
        $metricAssigmentOne = new RecordOfMetricAssignment($this->participantOne_client, '1');
        $metricAssigmentOne->insert($this->connection);

        $assignmentField_11 = new RecordOfAssignmentField($metricAssigmentOne, $this->metric, '11');
        $assignmentField_11->disabled = true;
        $assignmentField_12 = new RecordOfAssignmentField($metricAssigmentOne, $this->metricOne, '12');
        $assignmentField_11->insert($this->connection);
        $assignmentField_12->insert($this->connection);

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

        $uri = $this->participantUri . "/{$this->participantOne_client->id}/assign-metric";
        $this->put($uri, $this->assignMetricInput, $this->coordinator->personnel->token)
                ->seeJsonContains($metricAssignmentResponse)
                ->seeJsonContains($assignmentFieldResponse)
                ->seeJsonContains($assignmentFieldOneResponse)
                ->seeStatusCode(200);

        $metricAssignmentEntry = [
            "Participant_id" => $this->participantOne_client->id,
            "startDate" => (new \DateTime($this->assignMetricInput["startDate"]))->format("Y-m-d"),
            "endDate" => (new \DateTime($this->assignMetricInput["endDate"]))->format("Y-m-d"),
        ];
        $this->seeInDatabase("MetricAssignment", $metricAssignmentEntry);

        $assignmentFieldEntry = [
            'id' => $assignmentField_11->id,
            "target" => $this->assignMetricInput["assignmentFields"][0]["target"],
            "Metric_id" => $assignmentField_11->metric->id,
            'disabled' => false,
        ];
        $this->seeInDatabase("AssignmentField", $assignmentFieldEntry);
        $assignmentFieldOneEntry = [
            'id' => $assignmentField_12->id,
            "Metric_id" => $assignmentField_12->metric->id,
            'disabled' => true,
        ];
        $this->seeInDatabase("AssignmentField", $assignmentFieldOneEntry);
        $assignmentFieldTwoEntry = [
            "target" => $this->assignMetricInput["assignmentFields"][1]["target"],
            "Metric_id" => $this->assignMetricInput["assignmentFields"][1]["metricId"],
            'disabled' => false,
        ];
        $this->seeInDatabase("AssignmentField", $assignmentFieldEntry);
    }

    public function test_evaluate_pass_200()
    {
        $participantResponse = [
            "id" => $this->participant->id,
            "active" => $this->participant->active,
        ];
        $lastEvaluationResponse = [
            "status" => $this->evaluateInput["status"],
            "extendDays" => null,
            "submitTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
            "coordinator" => [
                "id" => $this->coordinator->id,
                "name" => $this->coordinator->personnel->getFullName(),
            ],
            "evaluationPlan" => [
                "id" => $this->evaluationPlan->id,
                "name" => $this->evaluationPlan->name,
            ],
        ];

        $uri = $this->participantUri . "/{$this->participant->id}/evaluate";
        $this->patch($uri, $this->evaluateInput, $this->coordinator->personnel->token)
                ->seeJsonContains($participantResponse)
                ->seeJsonContains($lastEvaluationResponse)
                ->seeStatusCode(200);

        $evaluationEntry = [
            "Participant_id" => $this->participant->id,
            "c_status" => $this->evaluateInput["status"],
            "extendDays" => null,
            "submitTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
            "Coordinator_id" => $this->coordinator->id,
            "EvaluationPlan_id" => $this->evaluationPlan->id,
        ];
        $this->seeInDatabase("Evaluation", $evaluationEntry);
    }
    public function test_evaluate_fail_disableParticipant_200()
    {
        $this->evaluateInput["status"] = "fail";

        $participantResponse = [
            "id" => $this->participant->id,
            "active" => false,
        ];
        $lastEvaluationResponse = [
            "status" => $this->evaluateInput["status"],
            "extendDays" => null,
            "submitTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
            "coordinator" => [
                "id" => $this->coordinator->id,
                "name" => $this->coordinator->personnel->getFullName(),
            ],
            "evaluationPlan" => [
                "id" => $this->evaluationPlan->id,
                "name" => $this->evaluationPlan->name,
            ],
        ];

        $uri = $this->participantUri . "/{$this->participant->id}/evaluate";
        $this->patch($uri, $this->evaluateInput, $this->coordinator->personnel->token)
                ->seeJsonContains($participantResponse)
                ->seeJsonContains($lastEvaluationResponse)
                ->seeStatusCode(200);

        $participantEntry = [
            "id" => $this->participant->id,
            "active" => false,
        ];
        $this->seeInDatabase("Participant", $participantEntry);

        $evaluationEntry = [
            "Participant_id" => $this->participant->id,
            "c_status" => $this->evaluateInput["status"],
            "extendDays" => null,
            "submitTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
            "Coordinator_id" => $this->coordinator->id,
            "EvaluationPlan_id" => $this->evaluationPlan->id,
        ];
        $this->seeInDatabase("Evaluation", $evaluationEntry);
    }
    public function test_evaluate_extend_200()
    {
        $this->evaluateInput["status"] = "extend";
        $this->evaluateInput["extendDays"] = 99;

        $participantResponse = [
            "id" => $this->participant->id,
            "active" => true,
        ];
        $lastEvaluationResponse = [
            "status" => $this->evaluateInput["status"],
            "extendDays" => 99,
            "submitTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
            "coordinator" => [
                "id" => $this->coordinator->id,
                "name" => $this->coordinator->personnel->getFullName(),
            ],
            "evaluationPlan" => [
                "id" => $this->evaluationPlan->id,
                "name" => $this->evaluationPlan->name,
            ],
        ];

        $uri = $this->participantUri . "/{$this->participant->id}/evaluate";
        $this->patch($uri, $this->evaluateInput, $this->coordinator->personnel->token)
                ->seeJsonContains($participantResponse)
                ->seeJsonContains($lastEvaluationResponse)
                ->seeStatusCode(200);

        $participantEntry = [
            "id" => $this->participant->id,
            "active" => true,
        ];
        $this->seeInDatabase("Participant", $participantEntry);

        $evaluationEntry = [
            "Participant_id" => $this->participant->id,
            "c_status" => $this->evaluateInput["status"],
            "extendDays" => 99,
            "submitTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
            "Coordinator_id" => $this->coordinator->id,
            "EvaluationPlan_id" => $this->evaluationPlan->id,
        ];
        $this->seeInDatabase("Evaluation", $evaluationEntry);
    }

    public function test_qualify_200()
    {
        $response = [
            "id" => $this->participant->id,
            "active" => false,
            "note" => "completed",
        ];
        $uri = $this->participantUri . "/{$this->participant->id}/qualify";
        $this->patch($uri, [], $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);

        $participantEntry = [
            "id" => $this->participant->id,
            "active" => false,
            "note" => "completed",
        ];
        $this->seeInDatabase("Participant", $participantEntry);
    }

}
