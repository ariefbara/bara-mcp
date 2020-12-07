<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator\Participant;

use Tests\Controllers\Personnel\AsProgramCoordinator\ParticipantTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\EvaluationPlan\RecordOfEvaluationReport;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfEvaluationPlan;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFeedbackForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class EvaluationReportController extends ParticipantTestCase
{

    protected $evaluationReportUri;
    protected $evaluationReportOne;
    protected $evaluationReportTwo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationReportUri = $this->participantUri . "/{$this->participant->id}/evaluation-reports";

        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("EvaluationPlan")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("EvaluationReport")->truncate();
        
        $program = $this->coordinator->program;
        $firm = $program->firm;
        
        $form = new RecordOfForm(1);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $feedbackForm = new RecordOfFeedbackForm($firm, $form);
        $this->connection->table("FeedbackForm")->insert($feedbackForm->toArrayForDbEntry());
        
        $evaluationPlanOne = new RecordOfEvaluationPlan($program, $feedbackForm, 1);
        $evaluationPlanTwo = new RecordOfEvaluationPlan($program, $feedbackForm, 2);
        $this->connection->table("EvaluationPlan")->insert($evaluationPlanOne->toArrayForDbEntry());
        $this->connection->table("EvaluationPlan")->insert($evaluationPlanTwo->toArrayForDbEntry());

        $formRecordOne = new RecordOfFormRecord($form, 1);
        $formRecordTwo = new RecordOfFormRecord($form, 2);
        $this->connection->table("FormRecord")->insert($formRecordOne->toArrayForDbEntry());
        $this->connection->table("FormRecord")->insert($formRecordTwo->toArrayForDbEntry());

        $this->evaluationReportOne = new RecordOfEvaluationReport(
                $evaluationPlanOne, $this->coordinator, $this->participant, $formRecordOne);
        $this->evaluationReportTwo = new RecordOfEvaluationReport(
                $evaluationPlanTwo, $this->coordinator, $this->participant, $formRecordTwo);
        $this->connection->table("EvaluationReport")->insert($this->evaluationReportOne->toArrayForDbEntry());
        $this->connection->table("EvaluationReport")->insert($this->evaluationReportTwo->toArrayForDbEntry());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("EvaluationPlan")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("EvaluationReport")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->evaluationReportOne->id,
            "coordinator" => [
                "id" => $this->evaluationReportOne->coordinator->id,
                "name" => $this->evaluationReportOne->coordinator->personnel->getFullName(),
            ],
            "evaluationPlan" => [
                "id" => $this->evaluationReportOne->evaluationPlan->id,
                "name" => $this->evaluationReportOne->evaluationPlan->name,
            ],
            "submitTime" => $this->evaluationReportOne->formRecord->submitTime,
            "stringFieldRecords" => [],
            "integerFieldRecords" => [],
            "textAreaFieldRecords" => [],
            "attachmentFieldRecords" => [],
            "singleSelectFieldRecords" => [],
            "multiSelectFieldRecords" => [],
        ];
        
        $uri = $this->evaluationReportUri . "/{$this->evaluationReportOne->id}";
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
                    "id" => $this->evaluationReportOne->id,
                    "coordinator" => [
                        "id" => $this->evaluationReportOne->coordinator->id,
                        "name" => $this->evaluationReportOne->coordinator->personnel->getFullName(),
                    ],
                    "evaluationPlan" => [
                        "id" => $this->evaluationReportOne->evaluationPlan->id,
                        "name" => $this->evaluationReportOne->evaluationPlan->name,
                    ],
                ],
                [
                    "id" => $this->evaluationReportTwo->id,
                    "coordinator" => [
                        "id" => $this->evaluationReportTwo->coordinator->id,
                        "name" => $this->evaluationReportTwo->coordinator->personnel->getFullName(),
                    ],
                    "evaluationPlan" => [
                        "id" => $this->evaluationReportTwo->evaluationPlan->id,
                        "name" => $this->evaluationReportTwo->evaluationPlan->name,
                    ],
                ],
            ],
        ];
        $this->get($this->evaluationReportUri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_includeEvaluationPlanQueryParameter()
    {
        $response = [
            "total" => 1,
            "list" => [
                [
                    "id" => $this->evaluationReportOne->id,
                    "coordinator" => [
                        "id" => $this->evaluationReportOne->coordinator->id,
                        "name" => $this->evaluationReportOne->coordinator->personnel->getFullName(),
                    ],
                    "evaluationPlan" => [
                        "id" => $this->evaluationReportOne->evaluationPlan->id,
                        "name" => $this->evaluationReportOne->evaluationPlan->name,
                    ],
                ],
            ],
        ];
        $uri = $this->evaluationReportUri . "?evaluationPlanId={$this->evaluationReportOne->evaluationPlan->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }

}
