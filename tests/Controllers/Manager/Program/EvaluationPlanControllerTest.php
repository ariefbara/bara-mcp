<?php

namespace Tests\Controllers\Manager\Program;

use Tests\Controllers\ {
    Manager\ProgramTestCase,
    RecordPreparation\Firm\Program\RecordOfEvaluationPlan,
    RecordPreparation\Firm\RecordOfFeedbackForm,
    RecordPreparation\Shared\RecordOfForm
};

class EvaluationPlanControllerTest extends ProgramTestCase
{

    protected $evaluationPlanUri;
    protected $evaluationPlan;
    protected $evaluationPlanOne_disabled;
    protected $feedbackForm;
    protected $createAndUpdateInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationPlanUri = $this->programUri . "/{$this->program->id}/evaluation-plans";
        
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("EvaluationPlan")->truncate();

        $firm = $this->program->firm;
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $this->feedbackForm = new RecordOfFeedbackForm($firm, $form);
        $this->connection->table("FeedbackForm")->insert($this->feedbackForm->toArrayForDbEntry());
        
        $this->evaluationPlan = new RecordOfEvaluationPlan($this->program, $this->feedbackForm, 0);
        $this->evaluationPlanOne_disabled = new RecordOfEvaluationPlan($this->program, $this->feedbackForm, 1);
        $this->evaluationPlanOne_disabled->disabled = true;
        $this->connection->table("EvaluationPlan")->insert($this->evaluationPlan->toArrayForDbEntry());
        $this->connection->table("EvaluationPlan")->insert($this->evaluationPlanOne_disabled->toArrayForDbEntry());
        
        $this->createAndUpdateInput = [
            "reportFormId" => $this->feedbackForm->id,
            "name" => "new evaluation plan name",
            "interval" => 60,
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("EvaluationPlan")->truncate();
    }
    
    public function test_create_201()
    {
        $response = [
            "name" => $this->createAndUpdateInput["name"],
            "interval" => $this->createAndUpdateInput["interval"],
            "disabled" => false,
            "reportForm" => [
                "id" =>  $this->feedbackForm->id,
                "name" =>  $this->feedbackForm->form->name,
            ],
        ];
        
        $this->post($this->evaluationPlanUri, $this->createAndUpdateInput, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(201);
        
        $evaluationPlanEntry = [
            "Program_id" => $this->program->id,
            "FeedbackForm_id" => $this->feedbackForm->id,
            "name" => $this->createAndUpdateInput["name"],
            "days_interval" => $this->createAndUpdateInput["interval"],
            "disabled" => false,
        ];
        $this->seeInDatabase("EvaluationPlan", $evaluationPlanEntry);
    }
    public function test_create_emptyName_400()
    {
        $this->createAndUpdateInput["name"] = "";
        $this->post($this->evaluationPlanUri, $this->createAndUpdateInput, $this->manager->token)
                ->seeStatusCode(400);
    }
    public function test_create_nullInterval_500_expectIntegerTypeError()
    {
        $this->createAndUpdateInput["interval"] = null;
        $this->post($this->evaluationPlanUri, $this->createAndUpdateInput, $this->manager->token)
                ->seeStatusCode(500);
    }
    
    public function test_update_200()
    {
        $response = [
            "id" => $this->evaluationPlan->id,
            "name" => $this->createAndUpdateInput["name"],
            "interval" => $this->createAndUpdateInput["interval"],
            "reportForm" => [
                "id" =>  $this->feedbackForm->id,
                "name" =>  $this->feedbackForm->form->name,
            ],
        ];
        
        $uri = $this->evaluationPlanUri . "/{$this->evaluationPlan->id}/update";
        $this->patch($uri, $this->createAndUpdateInput, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $evaluationPlanEntry = [
            "id" => $this->evaluationPlan->id,
            "FeedbackForm_id" => $this->feedbackForm->id,
            "name" => $this->createAndUpdateInput["name"],
            "days_interval" => $this->createAndUpdateInput["interval"],
        ];
        $this->seeInDatabase("EvaluationPlan", $evaluationPlanEntry);
    }
    public function test_update_emptyName_400()
    {
        $this->createAndUpdateInput["name"] = "";
        $uri = $this->evaluationPlanUri . "/{$this->evaluationPlan->id}/update";
        $this->patch($uri, $this->createAndUpdateInput, $this->manager->token)
                ->seeStatusCode(400);
    }
    public function test_update_nullInterval_500TypeError()
    {
        $this->createAndUpdateInput["interval"] = null;
        $uri = $this->evaluationPlanUri . "/{$this->evaluationPlan->id}/update";
        $this->patch($uri, $this->createAndUpdateInput, $this->manager->token)
                ->seeStatusCode(500);
    }
    
    public function test_disable_200()
    {
        $response = [
            "id" => $this->evaluationPlan->id,
            "disabled" => true,
        ];
        
        $uri = $this->evaluationPlanUri . "/{$this->evaluationPlan->id}/disable";
        $this->patch($uri, $this->createAndUpdateInput, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $evaluationPlanEntry = [
            "id" => $this->evaluationPlan->id,
            "disabled" => true,
        ];
        $this->seeInDatabase("EvaluationPlan", $evaluationPlanEntry);
    }
    
    public function test_enable_200()
    {
        $response = [
            "id" => $this->evaluationPlanOne_disabled->id,
            "disabled" => false,
        ];
        
        $uri = $this->evaluationPlanUri . "/{$this->evaluationPlanOne_disabled->id}/enable";
        $this->patch($uri, $this->createAndUpdateInput, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $evaluationPlanEntry = [
            "id" => $this->evaluationPlanOne_disabled->id,
            "disabled" => false,
        ];
        $this->seeInDatabase("EvaluationPlan", $evaluationPlanEntry);
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->evaluationPlan->id,
            "name" => $this->evaluationPlan->name,
            "interval" => $this->evaluationPlan->interval,
            "disabled" => $this->evaluationPlan->disabled,
            "reportForm" => [
                "id" => $this->evaluationPlan->feedbackFormForm->id,
                "name" => $this->evaluationPlan->feedbackFormForm->form->name,
            ],
        ];
        $uri = $this->evaluationPlanUri . "/{$this->evaluationPlan->id}";
        $this->get($uri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->evaluationPlan->id,
                    "name" => $this->evaluationPlan->name,
                    "interval" => $this->evaluationPlan->interval,
                    "disabled" => $this->evaluationPlan->disabled,
                    "reportForm" => [
                        "id" => $this->evaluationPlan->feedbackFormForm->id,
                        "name" => $this->evaluationPlan->feedbackFormForm->form->name,
                    ],
                ],
                [
                    "id" => $this->evaluationPlanOne_disabled->id,
                    "name" => $this->evaluationPlanOne_disabled->name,
                    "interval" => $this->evaluationPlanOne_disabled->interval,
                    "disabled" => $this->evaluationPlanOne_disabled->disabled,
                    "reportForm" => [
                        "id" => $this->evaluationPlanOne_disabled->feedbackFormForm->id,
                        "name" => $this->evaluationPlanOne_disabled->feedbackFormForm->form->name,
                    ],
                ],
            ],
        ];
        $this->get($this->evaluationPlanUri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }

}
