<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use DateTime;
use DateTimeImmutable;
use SharedContext\Domain\ValueObject\ParticipantStatus;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\RecordOfAssignmentFieldValue;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfAssignmentField;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfMetricAssignmentReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfEvaluation;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMetricAssignment;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfEvaluationPlan;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMetric;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;

class ParticipantControllerTest extends ParticipantTestCase
{
    protected $clientParticipant;
    protected $teamParticipant;
    protected $memberOne_t1;

    protected $metricAssignment;
    protected $metricOne;
    protected $metricTwo;
    protected $metricAssignmentFieldOne;
    protected $metricAssignmentFieldTwo;
    
    protected $metricAssignmentReportOne;
    protected $metricAssignmentReportTwo_latestApproved;
    protected $metricAssignmentReportThree_unapproved;
    
    protected $assignmentFieldValueOne_mar2_af1;
    protected $assignmentFieldValueTwo_mar2_af2;
    
    protected $assignMetricInput;
    
    protected $evaluationOne_latest;
    protected $evaluationTwo;
    protected $evaluationPlan;
    protected $evaluateInput;

    protected function setUp(): void
    {
        parent::setUp();
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
        $this->connection->table("ParticipantInvoice")->truncate();
        $this->connection->table("Invoice")->truncate();

        $program = $this->coordinator->program;
        $firm = $program->firm;
        
        $clientOne = new RecordOfClient($firm, 1);
        
        $teamOne = new RecordOfTeam($firm, $clientOne, 1);
        
        $this->memberOne_t1 = new RecordOfMember($teamOne, $clientOne, 1);
        
        $participantOne = new RecordOfParticipant($program, 1);
        $participantTwo = new RecordOfParticipant($program, 2);
        
        $this->clientParticipant = new RecordOfClientParticipant($clientOne, $participantOne);
        $this->teamParticipant = new RecordOfTeamProgramParticipation($teamOne, $participantTwo);
        
        $this->metricAssignment = new RecordOfMetricAssignment($participantOne, 1);
        
        $this->metricOne = new RecordOfMetric($program, 1);
        $this->metricTwo = new RecordOfMetric($program, 2);
        
        $this->metricAssignmentFieldOne = new RecordOfAssignmentField($this->metricAssignment, $this->metricOne, 1);
        $this->metricAssignmentFieldTwo = new RecordOfAssignmentField($this->metricAssignment, $this->metricTwo, 2);
        
        $this->metricAssignmentReportOne = new RecordOfMetricAssignmentReport($this->metricAssignment, 1);
        $this->metricAssignmentReportOne->observationTime = (new DateTime('-3 months'))->format('Y-m-d H:i:s');
        $this->metricAssignmentReportOne->approved = true;
        //
        $this->metricAssignmentReportTwo_latestApproved = new RecordOfMetricAssignmentReport($this->metricAssignment, 2);
        $this->metricAssignmentReportTwo_latestApproved->observationTime = (new DateTime('-2 months'))->format('Y-m-d H:i:s');
        $this->metricAssignmentReportTwo_latestApproved->approved = true;
        //
        $this->metricAssignmentReportThree_unapproved = new RecordOfMetricAssignmentReport($this->metricAssignment, 3);
        $this->metricAssignmentReportThree_unapproved->observationTime = (new DateTime('-1 months'))->format('Y-m-d H:i:s');
        
        $this->assignmentFieldValueOne_mar2_af1 = new RecordOfAssignmentFieldValue(
                $this->metricAssignmentReportTwo_latestApproved, $this->metricAssignmentFieldOne, 1);
        $this->assignmentFieldValueTwo_mar2_af2 = new RecordOfAssignmentFieldValue(
                $this->metricAssignmentReportTwo_latestApproved, $this->metricAssignmentFieldTwo, 2);
        
        $evaluationPlanOne = new RecordOfEvaluationPlan($program, null, 1);
        $evaluationPlanTwo = new RecordOfEvaluationPlan($program, null, 2);
        
        $this->evaluationOne_latest = new RecordOfEvaluation($participantOne, $evaluationPlanOne, $this->coordinator, 1);
        $this->evaluationOne_latest->submitTime = (new DateTime('-1 months'))->format('Y-m-d H:i:s');
        //
        $this->evaluationTwo = new RecordOfEvaluation($participantOne, $evaluationPlanTwo, $this->coordinator, 2);
        $this->evaluationTwo->submitTime = (new DateTime('-2 months'))->format('Y-m-d H:i:s');
        
        $this->assignMetricInput = [
            "startDate" => (new DateTime("+4 months"))->format("Y-m-d H:i:s"),
            "endDate" => (new DateTime("+6 months"))->format("Y-m-d H:i:s"),
            "assignmentFields" => [
                [
                    "metricId" => $this->metricOne->id,
                    "target" => 888888,
                ],
                [
                    "metricId" => $this->metricTwo->id,
                    "target" => 222222,
                ],
            ],
        ];
        //
        $this->evaluationPlan = new RecordOfEvaluationPlan($program, null, 0);
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
        $this->connection->table("ParticipantInvoice")->truncate();
        $this->connection->table("Invoice")->truncate();
    }
    
    protected function acceptRegisteredClientParticipant()
    {
        $this->clientParticipant->client->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        
        $uri = $this->participantUri . "/{$this->clientParticipant->participant->id}/accept-registered-participant";
        $this->patch($uri, [], $this->coordinator->personnel->token);
    }
    protected function acceptRegisteredTeamParticipant()
    {
        $this->teamParticipant->team->insert($this->connection);
        $this->teamParticipant->insert($this->connection);
        
        $this->memberOne_t1->client->insert($this->connection);
        $this->memberOne_t1->insert($this->connection);
        
        $uri = $this->participantUri . "/{$this->teamParticipant->participant->id}/accept-registered-participant";
        $this->patch($uri, [], $this->coordinator->personnel->token);
    }
    public function test_acceptRegisteredParticipant_200()
    {
$this->disableExceptionHandling();
        $this->acceptRegisteredClientParticipant();
        $this->seeStatusCode(200);
        
        $response = [
            "id" => $this->clientParticipant->participant->id,
            "status" => "ACTIVE",
            "programPriceSnapshot" => $this->clientParticipant->participant->programPrice,
            "client" => [
                'id' => $this->clientParticipant->client->id,
                'name' => $this->clientParticipant->client->getFullName(),
            ],
            "user" => null,
            "team" => null,
            "metricAssignment" => null,
            "lastEvaluation" => null,
        ];
        $this->seeJsonContains($response);
        
        $participantEntry = [
            "id" => $this->clientParticipant->participant->id,
            "status" => ParticipantStatus::ACTIVE,
        ];
        $this->seeInDatabase('Participant', $participantEntry);
    }
    public function test_AcceptRegisteredParticipant_paidProgram_changeToSettlementRequiredStatus_200()
    {
        $this->clientParticipant->participant->programPrice = 150000;
        $this->acceptRegisteredClientParticipant();
        $this->seeStatusCode(200);
        
        $response = [
            "id" => $this->clientParticipant->participant->id,
            "status" => "SETTLEMENT_REQUIRED",
            "programPriceSnapshot" => $this->clientParticipant->participant->programPrice,
            "client" => [
                'id' => $this->clientParticipant->client->id,
                'name' => $this->clientParticipant->client->getFullName(),
            ],
            "user" => null,
            "team" => null,
            "metricAssignment" => null,
            "lastEvaluation" => null,
        ];
        $this->seeJsonContains($response);
        
        $participantEntry = [
            "id" => $this->clientParticipant->participant->id,
            "status" => ParticipantStatus::SETTLEMENT_REQUIRED,
        ];
        $this->seeInDatabase('Participant', $participantEntry);
    }
    public function test_AcceptRegisteredParticipant_paidProgram_generateInvoice_200()
    {
        $this->clientParticipant->participant->programPrice = 150000;
        $this->acceptRegisteredClientParticipant();
        $this->seeStatusCode(200);
        
        $participantInvoiceEntry = [
            'Participant_id' => $this->clientParticipant->participant->id,
        ];
        $this->seeInDatabase('ParticipantInvoice', $participantInvoiceEntry);
        
        $invoiceEntry = [
            'settled' => false,
        ];
        $this->seeInDatabase('Invoice', $invoiceEntry);
    }
    public function test_acceptRegistedParticipant_nonRegisteredParticipant_403()
    {
        $this->clientParticipant->participant->status = ParticipantStatus::ACTIVE;
        $this->acceptRegisteredClientParticipant();
        $this->seeStatusCode(403);
    }
    public function test_acceptRegisteredParticipant_paidTeamParticipant_200()
    {
        $this->teamParticipant->participant->programPrice = 250000;
        $this->acceptRegisteredTeamParticipant();
        $this->seeStatusCode(200);
        
        $response = [
            "id" => $this->teamParticipant->participant->id,
            "status" => "SETTLEMENT_REQUIRED",
            "programPriceSnapshot" => $this->teamParticipant->participant->programPrice,
            "client" => null,
            "user" => null,
            "team" => [
                'id' => $this->teamParticipant->team->id,
                'name' => $this->teamParticipant->team->name,
                'members' => [
                    [
                        'id' => $this->memberOne_t1->id,
                        'client' => [
                            'id' => $this->memberOne_t1->client->id,
                            'name' => $this->memberOne_t1->client->getFullName(),
                        ],
                    ],
                ],
            ],
            "metricAssignment" => null,
            "lastEvaluation" => null,
        ];
        $this->seeJsonContains($response);
        
        $participantEntry = [
            "id" => $this->teamParticipant->participant->id,
            "status" => ParticipantStatus::SETTLEMENT_REQUIRED,
        ];
        $this->seeInDatabase('Participant', $participantEntry);
        
        $participantInvoiceEntry = [
            'Participant_id' => $this->teamParticipant->participant->id,
        ];
        $this->seeInDatabase('ParticipantInvoice', $participantInvoiceEntry);
        
        $invoiceEntry = [
            'settled' => false,
        ];
        $this->seeInDatabase('Invoice', $invoiceEntry);
    }
    public function test_acceptRegisteredParticipant_unmanagedParticipant_belongsToOtherProgram_403()
    {
        $otherProgram = new RecordOfProgram($this->coordinator->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->clientParticipant->participant->program = $otherProgram;
        
        $this->acceptRegisteredClientParticipant();
        $this->seeStatusCode(403);
    }
    
    protected function rejectRegisteredParticipant()
    {
        $this->clientParticipant->client->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        
        $uri = $this->participantUri . "/{$this->clientParticipant->participant->id}/reject-registered-participant";
        $this->patch($uri, [], $this->coordinator->personnel->token);
    }
    public function test_rejectRegisteredParticipant_200()
    {
        $this->rejectRegisteredParticipant();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->clientParticipant->participant->id,
            'status' => 'REJECTED'
        ];
        $this->seeJsonContains($response);
        
        $participantEntry = [
            'id' => $this->clientParticipant->participant->id,
            'status' => ParticipantStatus::REJECTED,
        ];
        $this->seeInDatabase('Participant', $participantEntry);
    }
    public function test_rejectRegisteredParticipant_nonRegisteredParticipant_403()
    {
        $this->clientParticipant->participant->status = ParticipantStatus::SETTLEMENT_REQUIRED;
        $this->rejectRegisteredParticipant();
        $this->seeStatusCode(403);
    }
    public function test_rejectRegisteredParticipant_unamnagedParticipant_belongsToOtherProgram_403()
    {
        $otherProgram = new RecordOfProgram($this->coordinator->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->clientParticipant->participant->program = $otherProgram;
        
        $this->rejectRegisteredParticipant();
        $this->seeStatusCode(403);
    }

    protected function show()
    {
        $this->clientParticipant->client->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
//        $this->memberOne->client->insert($this->connection);
//        $this->memberTwo->client->insert($this->connection);
//
//        $this->memberOne->insert($this->connection);
//        $this->memberTwo->insert($this->connection);

        $uri = $this->participantUri . "/{$this->clientParticipant->participant->id}";
        $this->get($uri, $this->coordinator->personnel->token);
    }
    public function test_show()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            "id" => $this->clientParticipant->participant->id,
            "status" => "REGISTERED",
            "programPriceSnapshot" => $this->clientParticipant->participant->programPrice,
            "client" => [
                'id' => $this->clientParticipant->client->id,
                'name' => $this->clientParticipant->client->getFullName(),
            ],
            "user" => null,
            "team" => null,
            "metricAssignment" => null,
            "lastEvaluation" => null,
        ];
        $this->seeJsonContains($response);
        
//        $this->connection->table("MetricAssignmentReport")->truncate();
//
//        $this->show();
//        $this->seeStatusCode(200);
//
//        $response = [
//            "id" => $this->teamParticipant->participant->id,
//            "enrolledTime" => $this->teamParticipant->participant->enrolledTime,
//            "active" => $this->teamParticipant->participant->active,
//            "note" => $this->teamParticipant->participant->note,
//            "user" => null,
//            "client" => null,
//            "team" => [
//                'id' => $this->teamParticipant->team->id,
//                'name' => $this->teamParticipant->team->name,
//                'members' => [
//                    [
//                        'id' => $this->memberOne->id,
//                        'client' => [
//                            'id' => $this->memberOne->client->id,
//                            'name' => $this->memberOne->client->getFullName(),
//                        ],
//                    ],
//                    [
//                        'id' => $this->memberTwo->id,
//                        'client' => [
//                            'id' => $this->memberTwo->client->id,
//                            'name' => $this->memberTwo->client->getFullName(),
//                        ],
//                    ],
//                ],
//            ],
//            "metricAssignment" => [
//                "startDate" => (new DateTime($this->metricAssignment->startDate))->format("Y-m-d"),
//                "endDate" => (new DateTime($this->metricAssignment->endDate))->format("Y-m-d"),
//                "assignmentFields" => [
//                    [
//                        "id" => $this->assignmentField->id,
//                        "target" => $this->assignmentField->target,
//                        "metric" => [
//                            "id" => $this->assignmentField->metric->id,
//                            "name" => $this->assignmentField->metric->name,
//                        ],
//                    ],
//                    [
//                        "id" => $this->assignmentFieldOne->id,
//                        "target" => $this->assignmentFieldOne->target,
//                        "metric" => [
//                            "id" => $this->assignmentFieldOne->metric->id,
//                            "name" => $this->assignmentFieldOne->metric->name,
//                        ],
//                    ],
//                ],
//                "lastMetricAssignmentReport" => null,
//            ],
//            "lastEvaluation" => null,
//        ];
//        $this->seeJsonContains($response);
    }
    public function test_show_unmanagedParticipant_belongsToOtherProgram_404()
    {
        $otherProgram = new RecordOfProgram($this->coordinator->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->clientParticipant->participant->program = $otherProgram;
        
        $this->show();
        $this->seeStatusCode(404);
    }
    public function test_show_hasMetricAssignmentReport_includeLatestApprovedReport_200()
    {
        $this->metricAssignment->insert($this->connection);
        //
        $this->metricAssignmentFieldOne->metric->insert($this->connection);
        $this->metricAssignmentFieldTwo->metric->insert($this->connection);
        //
        $this->metricAssignmentFieldOne->insert($this->connection);
        $this->metricAssignmentFieldTwo->insert($this->connection);
        
        $this->metricAssignmentReportOne->insert($this->connection);
        $this->metricAssignmentReportTwo_latestApproved->insert($this->connection);
        $this->metricAssignmentReportThree_unapproved->insert($this->connection);
        
        $this->assignmentFieldValueOne_mar2_af1->insert($this->connection);
        $this->assignmentFieldValueTwo_mar2_af2->insert($this->connection);
        
        $this->show();
        $response = [
            'id' => $this->clientParticipant->participant->id,
            "metricAssignment" => [
                "startDate" => (new DateTime($this->metricAssignment->startDate))->format("Y-m-d"),
                "endDate" => (new DateTime($this->metricAssignment->endDate))->format("Y-m-d"),
                "assignmentFields" => [
                    [
                        "id" => $this->metricAssignmentFieldOne->id,
                        "target" => $this->metricAssignmentFieldOne->target,
                        "metric" => [
                            "id" => $this->metricAssignmentFieldOne->metric->id,
                            "name" => $this->metricAssignmentFieldOne->metric->name,
                        ],
                    ],
                    [
                        "id" => $this->metricAssignmentFieldTwo->id,
                        "target" => $this->metricAssignmentFieldTwo->target,
                        "metric" => [
                            "id" => $this->metricAssignmentFieldTwo->metric->id,
                            "name" => $this->metricAssignmentFieldTwo->metric->name,
                        ],
                    ],
                ],
                "lastMetricAssignmentReport" => [
                    'id' => $this->metricAssignmentReportTwo_latestApproved->id,
                    'observationTime' => $this->metricAssignmentReportTwo_latestApproved->observationTime,
                    'submitTime' => $this->metricAssignmentReportTwo_latestApproved->submitTime,
                    'removed' => $this->metricAssignmentReportTwo_latestApproved->removed,
                    'assignmentFieldValues' => [
                        [
                            'id' => $this->assignmentFieldValueOne_mar2_af1->id,
                            'value' => $this->assignmentFieldValueOne_mar2_af1->inputValue,
                            'assignmentFieldId' => $this->assignmentFieldValueOne_mar2_af1->assignmentField->id,
                        ],
                        [
                            'id' => $this->assignmentFieldValueTwo_mar2_af2->id,
                            'value' => $this->assignmentFieldValueTwo_mar2_af2->inputValue,
                            'assignmentFieldId' => $this->assignmentFieldValueTwo_mar2_af2->assignmentField->id,
                        ],
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_hasEvaluation_200()
    {
        $this->evaluationOne_latest->evaluationPlan->insert($this->connection);
        $this->evaluationTwo->evaluationPlan->insert($this->connection);
        //
        $this->evaluationOne_latest->insert($this->connection);
        $this->evaluationTwo->insert($this->connection);
        
        $this->show();
        $response = [
            'id' => $this->clientParticipant->participant->id,
            'lastEvaluation' => [
                'id' => $this->evaluationOne_latest->id,
                'status' => $this->evaluationOne_latest->status,
                'extendDays' => $this->evaluationOne_latest->extendDays,
                'submitTime' => $this->evaluationOne_latest->submitTime,
                'coordinator' => [
                    'id' => $this->evaluationOne_latest->coordinator->id,
                    'name' => $this->evaluationOne_latest->coordinator->personnel->getFullName(),
                ],
                'evaluationPlan' => [
                    'id' => $this->evaluationOne_latest->evaluationPlan->id,
                    'name' => $this->evaluationOne_latest->evaluationPlan->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    public function test_show_personnelNotProgramCoordinator_403()
    {
        $uri = $this->participantUri . "/{$this->participant->id}";
        $this->get($uri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }

    protected function showAll()
    {
        $this->clientParticipant->client->insert($this->connection);
        $this->teamParticipant->team->insert($this->connection);
        //
        $this->clientParticipant->insert($this->connection);
        $this->teamParticipant->insert($this->connection);
        
        $this->memberOne_t1->insert($this->connection);
//        
//        $this->memberOne->client->insert($this->connection);
//        $this->memberTwo->client->insert($this->connection);
//
//        $this->memberOne->insert($this->connection);
//        $this->memberTwo->insert($this->connection);

        $this->get($this->participantUri, $this->coordinator->personnel->token);
    }
    public function test_showAll()
    {
$this->disableExceptionHandling();
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 3, 
            'list' => [
                [
                    "id" => $this->clientParticipant->participant->id,
                    "status" => "REGISTERED",
                    "programPriceSnapshot" => $this->clientParticipant->participant->programPrice,
                    "client" => [
                        'id' => $this->clientParticipant->client->id,
                        'name' => $this->clientParticipant->client->getFullName(),
                    ],
                    "user" => null,
                    "team" => null,
                    "metricAssignment" => null,
                    "lastEvaluation" => null,
                ],
                [
                    "id" => $this->teamParticipant->participant->id,
                    "status" => "REGISTERED",
                    "programPriceSnapshot" => $this->clientParticipant->participant->programPrice,
                    "client" => null,
                    "user" => null,
                    "team" => [
                        "id" => $this->teamParticipant->team->id,
                        "name" => $this->teamParticipant->team->name,
                        'members' => [
                            [
                                'id' => $this->memberOne_t1->id,
                                'client' => [
                                    'id' => $this->memberOne_t1->client->id,
                                    'name' => $this->memberOne_t1->client->getFullName(),
                                ],
                            ],
                        ],
                    ],
                    "metricAssignment" => null,
                    "lastEvaluation" => null,
                ],
                [
                    "id" => $this->userParticipant->participant->id,
                    "status" => "REGISTERED",
                    "programPriceSnapshot" => $this->userParticipant->participant->programPrice,
                    "client" => null,
                    "team" => null,
                    "user" => [
                        "id" => $this->userParticipant->user->id,
                        "name" => $this->userParticipant->user->getFullName(),
                    ],
                    "metricAssignment" => null,
                    "lastEvaluation" => null,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_filterByStatus_200()
    {
        $this->participantUri .= "?status[]=1&status[]=2";
        $this->clientParticipant->participant->status = 2;
        $this->teamParticipant->participant->status = 3;
        
        $this->showAll();
        $this->seeJsonContains(['total' => 2]);
        
        $this->seeJsonContains(['id' => $this->clientParticipant->participant->id]);
        $this->seeJsonContains(['id' => $this->userParticipant->participant->id]);
        $this->seeJsonDoesntContains(['id' => $this->teamParticipant->participant->id]);
    }
    public function test_showAll_filterByName_200()
    {
        $this->participantUri .= "?searchByName=user";
        
        $this->showAll();
        $this->seeJsonContains(['total' => 1]);
        
        $this->seeJsonDoesntContains(['id' => $this->clientParticipant->participant->id]);
        $this->seeJsonContains(['id' => $this->userParticipant->participant->id]);
        $this->seeJsonDoesntContains(['id' => $this->teamParticipant->participant->id]);
    }
    public function test_showAll_excludeParticpiantOfOtherProgram_200()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->clientParticipant->participant->program = $otherProgram;
        
        $this->showAll();
        $this->seeJsonContains(['total' => 2]);
        
        $this->seeJsonDoesntContains(['id' => $this->clientParticipant->participant->id]);
        $this->seeJsonContains(['id' => $this->userParticipant->participant->id]);
        $this->seeJsonContains(['id' => $this->teamParticipant->participant->id]);
    }

    protected function assignMetric()
    {
        $this->clientParticipant->client->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        $this->metricOne->insert($this->connection);
        $this->metricTwo->insert($this->connection);
        
        $uri = $this->participantUri . "/{$this->clientParticipant->participant->id}/assign-metric";
        $this->put($uri, $this->assignMetricInput, $this->coordinator->personnel->token);
    }
    public function test_assignMetric_200()
    {
        $this->assignMetric();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->clientParticipant->participant->id,
            'metricAssignment' => [
                "startDate" => (new DateTime($this->assignMetricInput["startDate"]))->format("Y-m-d"),
                "endDate" => (new DateTime($this->assignMetricInput["endDate"]))->format("Y-m-d"),
                'assignmentFields' => [
                    [
                        
                    ],
                ],
                'lastMetricAssignmentReport' => null,
            ],
        ];
        
        $metricAssignmentResponse = [
            "startDate" => (new DateTime($this->assignMetricInput["startDate"]))->format("Y-m-d"),
            "endDate" => (new DateTime($this->assignMetricInput["endDate"]))->format("Y-m-d"),
            'lastMetricAssignmentReport' => null,
        ];
        $this->seeJsonContains($metricAssignmentResponse);
        
        $assignmentFieldOneResponse = [
            "target" => $this->assignMetricInput["assignmentFields"][0]["target"],
            "metric" => [
                "id" => $this->assignMetricInput["assignmentFields"][0]["metricId"],
                "name" => $this->metricOne->name,
            ],
        ];
        $this->seeJsonContains($assignmentFieldOneResponse);
        
        $assignmentFieldTwoResponse = [
            "target" => $this->assignMetricInput["assignmentFields"][1]["target"],
            "metric" => [
                "id" => $this->assignMetricInput["assignmentFields"][1]["metricId"],
                "name" => $this->metricTwo->name,
            ],
        ];
        $this->seeJsonContains($assignmentFieldTwoResponse);

        $metricAssignmentEntry = [
            "Participant_id" => $this->clientParticipant->participant->id,
            "startDate" => (new DateTime($this->assignMetricInput["startDate"]))->format("Y-m-d"),
            "endDate" => (new DateTime($this->assignMetricInput["endDate"]))->format("Y-m-d"),
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
        $this->metricAssignment->insert($this->connection);
        $this->metricAssignmentFieldOne->insert($this->connection);
        
        $this->assignMetric();
        $this->seeStatusCode(200);
        
        $metricAssignmentEntry = [
            "Participant_id" => $this->clientParticipant->participant->id,
            "startDate" => (new DateTime($this->assignMetricInput["startDate"]))->format("Y-m-d"),
            "endDate" => (new DateTime($this->assignMetricInput["endDate"]))->format("Y-m-d"),
        ];
        $this->seeInDatabase("MetricAssignment", $metricAssignmentEntry);

        $assignmentFieldEntry = [
            'id' => $this->metricAssignmentFieldOne->id,
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

    protected function evaluate()
    {
        $this->clientParticipant->client->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        
        $this->evaluationPlan->insert($this->connection);
        
        $uri = $this->participantUri . "/{$this->clientParticipant->participant->id}/evaluate";
        $this->patch($uri, $this->evaluateInput, $this->coordinator->personnel->token);
    }
    public function test_evaluate_pass_200()
    {
        $this->clientParticipant->participant->status = ParticipantStatus::ACTIVE;
        $this->evaluate();
        $this->seeStatusCode(200);
        
        $response = [
            "id" => $this->clientParticipant->participant->id,
            "status" => "ACTIVE",
        ];
        $this->seeJsonContains($response);
        $evaluationResponse = [
            'status' => 'pass',
            'extendDays' => null,
            'coordinator' => [
                'id' => $this->coordinator->id,
                'name' => $this->coordinator->personnel->getFullName(),
            ],
            'evaluationPlan' => [
                'id' => $this->evaluationPlan->id,
                'name' => $this->evaluationPlan->name,
            ],
        ];
        $this->seeJsonContains($evaluationResponse);
    }
    public function test_evaluate_fail_setFailed_200()
    {
        $this->clientParticipant->participant->status = ParticipantStatus::ACTIVE;
        $this->evaluateInput["status"] = "fail";
        $this->evaluate();
        
        $response = [
            "id" => $this->clientParticipant->participant->id,
            "status" => "FAILED",
        ];
        $this->seeJsonContains($response);
        $evaluationResponse = [
            'status' => 'fail',
            'extendDays' => null,
            'coordinator' => [
                'id' => $this->coordinator->id,
                'name' => $this->coordinator->personnel->getFullName(),
            ],
            'evaluationPlan' => [
                'id' => $this->evaluationPlan->id,
                'name' => $this->evaluationPlan->name,
            ],
        ];
        $this->seeJsonContains($evaluationResponse);
    }
    public function test_evaluate_extend_200()
    {
        $this->clientParticipant->participant->status = ParticipantStatus::ACTIVE;
        $this->evaluateInput["status"] = "extend";
        $this->evaluateInput["extendDays"] = 99;
        $this->evaluate();
        
        $response = [
            "id" => $this->clientParticipant->participant->id,
            "status" => "ACTIVE",
        ];
        $this->seeJsonContains($response);
        $evaluationResponse = [
            'status' => 'extend',
            'extendDays' => 99,
            'coordinator' => [
                'id' => $this->coordinator->id,
                'name' => $this->coordinator->personnel->getFullName(),
            ],
            'evaluationPlan' => [
                'id' => $this->evaluationPlan->id,
                'name' => $this->evaluationPlan->name,
            ],
        ];
        $this->seeJsonContains($evaluationResponse);
    }
    public function test_evaluate_inactiveParticipant_403()
    {
        $this->evaluate();
        $this->seeStatusCode(403);
    }

    protected function qualify()
    {
        $this->clientParticipant->client->insert($this->connection);
        $this->clientParticipant->participant->insert($this->connection);
        
        $uri = $this->participantUri . "/{$this->clientParticipant->participant->id}/qualify";
        $this->patch($uri, [], $this->coordinator->personnel->token);
    }
    public function test_qualify_200()
    {
$this->disableExceptionHandling();
        $this->clientParticipant->participant->status = ParticipantStatus::ACTIVE;
        $this->qualify();
        $this->seeStatusCode(200);
        
        $response = [
            "id" => $this->clientParticipant->participant->id,
            "status" => 'COMPLETED',
        ];
        $this->seeJsonContains($response);
        
        $participantEntry = [
            "id" => $this->clientParticipant->participant->id,
            "status" => ParticipantStatus::COMPLETED,
        ];
        $this->seeInDatabase('Participant', $participantEntry);
    }
    public function test_qualify_inactiveParticipant_403()
    {
        $this->qualify();
        $this->seeStatusCode(403);
    }

}
