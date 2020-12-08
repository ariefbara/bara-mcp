<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator\Participant;

use Tests\Controllers\Personnel\AsProgramCoordinator\ParticipantTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfEvaluation;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfEvaluationPlan;

class EvaluationControllerTest extends ParticipantTestCase
{
    protected $evaluationUri;
    protected $evaluationPlan;
    protected $evaluationOne;
    protected $evaluationTwo;
    protected $evaluateInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationUri = $this->participantUri . "/{$this->participant->id}/evaluations";
        
        $this->connection->table("EvaluationPlan")->truncate();
        $this->connection->table("Evaluation")->truncate();
        
        $program = $this->coordinator->program;
        
        $this->evaluationPlan = new RecordOfEvaluationPlan($program, null, 99);
        $evaluationPlanOne = new RecordOfEvaluationPlan($program, null, 1);
        $evaluationPlanTwo = new RecordOfEvaluationPlan($program, null, 2);
        $this->connection->table("EvaluationPlan")->insert($this->evaluationPlan->toArrayForDbEntry());
        $this->connection->table("EvaluationPlan")->insert($evaluationPlanOne->toArrayForDbEntry());
        $this->connection->table("EvaluationPlan")->insert($evaluationPlanTwo->toArrayForDbEntry());
        
        $this->evaluationOne = new RecordOfEvaluation($this->participant, $evaluationPlanOne, $this->coordinator, 1);
        $this->evaluationTwo = new RecordOfEvaluation($this->participant, $evaluationPlanTwo, $this->coordinator, 2);
        $this->connection->table("Evaluation")->insert($this->evaluationOne->toArrayForDbEntry());
        $this->connection->table("Evaluation")->insert($this->evaluationTwo->toArrayForDbEntry());
        
        $this->evaluateInput = [
            "evaluationPlanId" => $this->evaluationPlan->id,
            "status" => "pass",
            "extendDays" => null,
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("EvaluationPlan")->truncate();
        $this->connection->table("Evaluation")->truncate();
    }
    
    public function test_evaluate_pass_200()
    {
        $uri = $this->evaluationUri;
        $this->post($uri, $this->evaluateInput, $this->coordinator->personnel->token)
                ->seeStatusCode(200);
        
        $evaluationEntry = [
            "Participant_id" => $this->participant->id,
            "c_status" => $this->evaluateInput["status"],
            "extendDays" => null,
            "submitTime" => (new \DateTimeImmutable())->format("Y-m-d H:i:s"),
            "Coordinator_id" => $this->coordinator->id,
            "EvaluationPlan_id" => $this->evaluationPlan->id,
        ];
        $this->seeInDatabase("Evaluation", $evaluationEntry);
    }
    public function test_evaluate_fail_disableParticipant_200()
    {
        $this->evaluateInput["status"] = "fail";
        
        $uri = $this->evaluationUri;
        $this->post($uri, $this->evaluateInput, $this->coordinator->personnel->token)
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
            "submitTime" => (new \DateTimeImmutable())->format("Y-m-d H:i:s"),
            "Coordinator_id" => $this->coordinator->id,
            "EvaluationPlan_id" => $this->evaluationPlan->id,
        ];
        $this->seeInDatabase("Evaluation", $evaluationEntry);
    }
    public function test_evaluate_extend_200()
    {
        $this->evaluateInput["status"] = "extend";
        $this->evaluateInput["extendDays"] = 99;
        
        $uri = $this->evaluationUri;
        $this->post($uri, $this->evaluateInput, $this->coordinator->personnel->token)
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
            "submitTime" => (new \DateTimeImmutable())->format("Y-m-d H:i:s"),
            "Coordinator_id" => $this->coordinator->id,
            "EvaluationPlan_id" => $this->evaluationPlan->id,
        ];
        $this->seeInDatabase("Evaluation", $evaluationEntry);
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->evaluationOne->id,
            "submitTime" => $this->evaluationOne->submitTime,
            "status" => $this->evaluationOne->status,
            "extendDays" => $this->evaluationOne->extendDays,
            "evaluationPlan" => [
                "id" => $this->evaluationOne->evaluationPlan->id,
                "name" => $this->evaluationOne->evaluationPlan->name,
            ],
            "coordinator" => [
                "id" => $this->evaluationOne->coordinator->id,
                "name" => $this->evaluationOne->coordinator->personnel->getFullName(),
            ],
        ];
        $uri = $this->evaluationUri . "/{$this->evaluationOne->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->evaluationOne->id,
                    "submitTime" => $this->evaluationOne->submitTime,
                    "status" => $this->evaluationOne->status,
                    "extendDays" => $this->evaluationOne->extendDays,
                    "evaluationPlan" => [
                        "id" => $this->evaluationOne->evaluationPlan->id,
                        "name" => $this->evaluationOne->evaluationPlan->name,
                    ],
                    "coordinator" => [
                        "id" => $this->evaluationOne->coordinator->id,
                        "name" => $this->evaluationOne->coordinator->personnel->getFullName(),
                    ],
                ],
                [
                    "id" => $this->evaluationTwo->id,
                    "submitTime" => $this->evaluationTwo->submitTime,
                    "status" => $this->evaluationTwo->status,
                    "extendDays" => $this->evaluationTwo->extendDays,
                    "evaluationPlan" => [
                        "id" => $this->evaluationTwo->evaluationPlan->id,
                        "name" => $this->evaluationTwo->evaluationPlan->name,
                    ],
                    "coordinator" => [
                        "id" => $this->evaluationTwo->coordinator->id,
                        "name" => $this->evaluationTwo->coordinator->personnel->getFullName(),
                    ],
                ],
            ],
        ];
        $this->get($this->evaluationUri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
 