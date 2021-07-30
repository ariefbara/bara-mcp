<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use Tests\Controllers\Personnel\ProgramConsultation\ProgramConsultationTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfEvaluationPlan;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFeedbackForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class EvaluationPlanControllerTest extends ProgramConsultationTestCase
{

    protected $evaluationPlanUri;
    protected $evaluationPlan;
    protected $evaluationPlanOne_disabled;
    protected $feedbackForm;
    protected $mission;
    protected $createAndUpdateInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationPlanUri = $this->programConsultationUri . "/evaluation-plans";
        
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("Mission")->truncate();
        $this->connection->table("EvaluationPlan")->truncate();

        $program = $this->programConsultation->program;
        $firm = $program->firm;
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $this->feedbackForm = new RecordOfFeedbackForm($firm, $form);
        $this->connection->table("FeedbackForm")->insert($this->feedbackForm->toArrayForDbEntry());
        
        $this->mission = new RecordOfMission($program, null, '99', null);
        $this->mission->insert($this->connection);
        
        $this->evaluationPlan = new RecordOfEvaluationPlan($program, $this->feedbackForm, 0, $this->mission);
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
        $this->connection->table("Mission")->truncate();
        $this->connection->table("EvaluationPlan")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->evaluationPlan->id,
            "name" => $this->evaluationPlan->name,
            "interval" => $this->evaluationPlan->interval,
            "disabled" => $this->evaluationPlan->disabled,
            "reportForm" => [
                "id" => $this->evaluationPlan->feedbackForm->id,
                "name" => $this->evaluationPlan->feedbackForm->form->name,
                "description" => $this->evaluationPlan->feedbackForm->form->description,
                'stringFields' => [],
                'integerFields' => [],
                'textAreaFields' => [],
                'attachmentFields' => [],
                'singleSelectFields' => [],
                'multiSelectFields' => [],
            ],
            "mission" => [
                'id' => $this->mission->id,
                'name' => $this->mission->name,
            ],
        ];
        $uri = $this->evaluationPlanUri . "/{$this->evaluationPlan->id}";
        $this->get($uri, $this->personnel->token)
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
                        "id" => $this->evaluationPlan->feedbackForm->id,
                        "name" => $this->evaluationPlan->feedbackForm->form->name,
                    ],
                    "mission" => [
                        'id' => $this->mission->id,
                        'name' => $this->mission->name,
                    ],
                ],
                [
                    "id" => $this->evaluationPlanOne_disabled->id,
                    "name" => $this->evaluationPlanOne_disabled->name,
                    "interval" => $this->evaluationPlanOne_disabled->interval,
                    "disabled" => $this->evaluationPlanOne_disabled->disabled,
                    "reportForm" => [
                        "id" => $this->evaluationPlanOne_disabled->feedbackForm->id,
                        "name" => $this->evaluationPlanOne_disabled->feedbackForm->form->name,
                    ],
                    "mission" => null,
                ],
            ],
        ];
        $this->get($this->evaluationPlanUri, $this->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }

}
