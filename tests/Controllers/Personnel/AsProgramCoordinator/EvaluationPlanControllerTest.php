<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfEvaluationPlan,
    Firm\RecordOfFeedbackForm,
    Shared\RecordOfForm
};

class EvaluationPlanControllerTest extends AsProgramCoordinatorTestCase
{
    protected $evaluationPlanUri;
    protected $evaluationPlan;
    protected $evaluationPlanOne_disabled;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationPlanUri = $this->asProgramCoordinatorUri . "/evaluation-plans";
        
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("EvaluationPlan")->truncate();

        $program = $this->coordinator->program;
        $firm = $program->firm;
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $this->feedbackForm = new RecordOfFeedbackForm($firm, $form);
        $this->connection->table("FeedbackForm")->insert($this->feedbackForm->toArrayForDbEntry());
        
        $this->evaluationPlan = new RecordOfEvaluationPlan($program, $this->feedbackForm, 0);
        $this->evaluationPlanOne_disabled = new RecordOfEvaluationPlan($program, $this->feedbackForm, 1);
        $this->evaluationPlanOne_disabled->disabled = true;
        $this->connection->table("EvaluationPlan")->insert($this->evaluationPlan->toArrayForDbEntry());
        $this->connection->table("EvaluationPlan")->insert($this->evaluationPlanOne_disabled->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("EvaluationPlan")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->evaluationPlan->id,
            "name" => $this->evaluationPlan->name,
            "interval" => $this->evaluationPlan->interval,
            "reportForm" => [
                "id" => $this->evaluationPlan->feedbackFormForm->id,
                "name" => $this->evaluationPlan->feedbackFormForm->form->name,
                "description" => $this->evaluationPlan->feedbackFormForm->form->description,
                "attachmentFields" => [],
                "stringFields" => [],
                "integerFields" => [],
                "textAreaFields" => [],
                "singleSelectFields" => [],
                "multiSelectFields" => [],
            ],
        ];
        $uri = $this->evaluationPlanUri . "/{$this->evaluationPlan->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_inactiveCoordinator_403()
    {
        $uri = $this->evaluationPlanUri . "/{$this->evaluationPlan->id}";
        $this->get($uri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 1,
            "list" => [
                [
                    "id" => $this->evaluationPlan->id,
                    "name" => $this->evaluationPlan->name,
                    "interval" => $this->evaluationPlan->interval,
                    "reportForm" => [
                        "id" => $this->evaluationPlan->feedbackFormForm->id,
                        "name" => $this->evaluationPlan->feedbackFormForm->form->name,
                    ],
                ],
            ],
        ];
        $this->get($this->evaluationPlanUri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveCoordinator_403()
    {
        $this->get($this->evaluationPlanUri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }
}
