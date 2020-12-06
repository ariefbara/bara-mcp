<?php

namespace Tests\Controllers\Manager\Program;

use Tests\Controllers\ {
    Manager\ProgramTestCase,
    RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant,
    RecordPreparation\Firm\Program\RecordOfActivityType,
    RecordPreparation\Firm\RecordOfFeedbackForm,
    RecordPreparation\JwtHeaderTokenGenerator,
    RecordPreparation\Shared\RecordOfForm
};

class ActivityTypeControllerTest extends ProgramTestCase
{
    protected $feedbackForm;
    protected $activityTypeUri;
    protected $activityType;
    protected $activityTypeOne;
    protected $activityParticipant_00;
    protected $activityParticipant_01;
    protected $createInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activityTypeUri = $this->programUri . "/{$this->program->id}/activity-types";
        
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        
        $this->activityType = new RecordOfActivityType($this->program, 0);
        $this->activityTypeOne = new RecordOfActivityType($this->program, 1);
        $this->activityTypeOne->disabled = true;
        $this->connection->table("ActivityType")->insert($this->activityType->toArrayForDbEntry());
        $this->connection->table("ActivityType")->insert($this->activityTypeOne->toArrayForDbEntry());
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $this->feedbackForm = new RecordOfFeedbackForm($this->firm, $form);
        $this->connection->table("FeedbackForm")->insert($this->feedbackForm->toArrayForDbEntry());
        
        $this->activityParticipant_00 = new RecordOfActivityParticipant($this->activityType, $this->feedbackForm, "00");
        $this->activityParticipant_00->disabled = true;
        $this->activityParticipant_01 = new RecordOfActivityParticipant($this->activityType, $this->feedbackForm, "01");
        $this->activityParticipant_01->participantType = "participant";
        $this->connection->table("ActivityParticipant")->insert($this->activityParticipant_00->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($this->activityParticipant_01->toArrayForDbEntry());
        
        $this->createInput = [
            "name" => "new activity type name",
            "description" => "new activity type description",
            "participants" => [
                [
                    "participantType" => "coordinator",
                    "canInitiate" => true,
                    "canAttend" => false,
                    "feedbackFormId" => null,
                ],
                [
                    "participantType" => "consultant",
                    "canInitiate" => false,
                    "canAttend" => true,
                    "feedbackFormId" => $this->feedbackForm->id,
                ],
            ],
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
    }

    public function test_create_201()
    {
        $this->connection->table("ActivityParticipant")->truncate();
        
        $activityTypeResponse = [
            "name" => $this->createInput["name"],
            "description" => $this->createInput["description"],
            "disabled" => false,
        ];
        $participantResponse = [
            "participantType" => $this->createInput["participants"][0]["participantType"],
            "canInitiate" => $this->createInput["participants"][0]["canInitiate"],
            "canAttend" => $this->createInput["participants"][0]["canAttend"],
            "feedbackForm" => null,
            "disabled" => false,
        ];
        $participantOneResponse = [
            "participantType" => $this->createInput["participants"][1]["participantType"],
            "canInitiate" => $this->createInput["participants"][1]["canInitiate"],
            "canAttend" => $this->createInput["participants"][1]["canAttend"],
            "feedbackForm" => [
                "id" => $this->feedbackForm->id,
                "name" => $this->feedbackForm->form->name,
                "description" => $this->feedbackForm->form->description,
            ],
            "disabled" => false,
        ];
        
        $this->post($this->activityTypeUri, $this->createInput, $this->manager->token)
                ->seeJsonContains($activityTypeResponse)
                ->seeJsonContains($participantResponse)
                ->seeJsonContains($participantOneResponse)
                ->seeStatusCode(201);
        
        $activityTypeEntry = [
            "Program_id" => $this->program->id,
            "name" => $this->createInput["name"],
            "description" => $this->createInput["description"],
            "disabled" => false,
        ];
        $this->seeInDatabase("ActivityType", $activityTypeEntry);
        
        $participantEntry = [
            "participantType" => $this->createInput["participants"][0]["participantType"],
            "canInitiate" => $this->createInput["participants"][0]["canInitiate"],
            "canAttend" => $this->createInput["participants"][0]["canAttend"],
            "FeedbackForm_id" => null,
            "disabled" => false,
        ];
        $this->seeInDatabase("ActivityParticipant", $participantEntry);
        
        $participantOneEntry = [
            "participantType" => $this->createInput["participants"][1]["participantType"],
            "canInitiate" => $this->createInput["participants"][1]["canInitiate"],
            "canAttend" => $this->createInput["participants"][1]["canAttend"],
            "FeedbackForm_id" => $this->feedbackForm->id,
            "disabled" => false,
        ];
        $this->seeInDatabase("ActivityParticipant", $participantOneEntry);
    }
    
    public function test_update_200()
    {
        $activityTypeResponse = [
            "id" => $this->activityType->id,
            "name" => $this->createInput["name"],
            "description" => $this->createInput["description"],
            "disabled" => false,
        ];
        $reenabledCoordinatorTypeResponse = [
            "id" => $this->activityParticipant_00->id,
            "participantType" => "coordinator",
            "canInitiate" => $this->createInput["participants"][0]["canInitiate"],
            "canAttend" => $this->createInput["participants"][0]["canAttend"],
            "feedbackForm" => null,
            "disabled" => false,
        ];
        $newConsultantTypeResponse = [
            "participantType" => $this->createInput["participants"][1]["participantType"],
            "canInitiate" => $this->createInput["participants"][1]["canInitiate"],
            "canAttend" => $this->createInput["participants"][1]["canAttend"],
            "feedbackForm" => [
                "id" => $this->feedbackForm->id,
                "name" => $this->feedbackForm->form->name,
                "description" => $this->feedbackForm->form->description,
            ],
            "disabled" => false,
        ];
        $disabledParticipantTypeResponse = [
            "id" => $this->activityParticipant_01->id,
            "disabled" => true,
        ];
        
        $uri = $this->activityTypeUri . "/{$this->activityType->id}/update";
        $this->patch($uri, $this->createInput, $this->manager->token)
                ->seeJsonContains($activityTypeResponse)
                ->seeJsonContains($reenabledCoordinatorTypeResponse)
                ->seeJsonContains($newConsultantTypeResponse)
                ->seeJsonContains($disabledParticipantTypeResponse)
                ->seeStatusCode(200);
        
        $activityTypeEntry = [
            "id" => $this->activityType->id,
            "name" => $this->createInput["name"],
            "description" => $this->createInput["description"],
            "disabled" => false,
        ];
        $this->seeInDatabase("ActivityType", $activityTypeEntry);
        
        $reenableCoordinatorTypeEntry = [
            "id" => $this->activityParticipant_00->id,
            "participantType" => $this->activityParticipant_00->participantType,
            "canInitiate" => $this->createInput["participants"][0]["canInitiate"],
            "canAttend" => $this->createInput["participants"][0]["canAttend"],
            "FeedbackForm_id" => null,
            "disabled" => false,
        ];
        $this->seeInDatabase("ActivityParticipant", $reenableCoordinatorTypeEntry);
        
        $consultantEntry = [
            "participantType" => $this->createInput["participants"][1]["participantType"],
            "canInitiate" => $this->createInput["participants"][1]["canInitiate"],
            "canAttend" => $this->createInput["participants"][1]["canAttend"],
            "FeedbackForm_id" => $this->feedbackForm->id,
            "disabled" => false,
        ];
        $this->seeInDatabase("ActivityParticipant", $consultantEntry);
        
        $disableParticipantTypeEntry = [
            "id" => $this->activityParticipant_01->id,
            "disabled" => true,
        ];
        $this->seeInDatabase("ActivityParticipant", $disableParticipantTypeEntry);
    }
    
    public function test_disable_200()
    {
        $response = [
            "id" => $this->activityType->id,
            "disabled" => true,
        ];
        
        $uri = $this->activityTypeUri . "/{$this->activityType->id}/disable";
        $this->patch($uri, $this->createInput, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $activityTypeEntry = [
            "id" => $this->activityType->id,
            "disabled" => true,
        ];
        $this->seeInDatabase("ActivityType", $activityTypeEntry);
    }
    
    public function test_enable_200()
    {
        $response = [
            "id" => $this->activityTypeOne->id,
            "disabled" => false,
        ];
        
        $uri = $this->activityTypeUri . "/{$this->activityTypeOne->id}/enable";
        $this->patch($uri, $this->createInput, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $activityTypeEntry = [
            "id" => $this->activityTypeOne->id,
            "disabled" => false,
        ];
        $this->seeInDatabase("ActivityType", $activityTypeEntry);
    }
    
    public function test_show_200()
    {
        $reponse = [
            "id" => $this->activityType->id,
            "name" => $this->activityType->name,
            "description" => $this->activityType->description,
            "disabled" => $this->activityType->disabled,
            "participants" => [
                [
                    "id" => $this->activityParticipant_00->id,
                    "participantType" => $this->activityParticipant_00->participantType,
                    "canInitiate" => $this->activityParticipant_00->canInitiate,
                    "canAttend" => $this->activityParticipant_00->canAttend,
                    "disabled" => $this->activityParticipant_00->disabled,
                    "feedbackForm" => [
                        "id" => $this->activityParticipant_00->feedbackForm->id,
                        "name" => $this->activityParticipant_00->feedbackForm->form->name,
                        "description" => $this->activityParticipant_00->feedbackForm->form->description,
                    ],
                ],
                [
                    "id" => $this->activityParticipant_01->id,
                    "participantType" => $this->activityParticipant_01->participantType,
                    "canInitiate" => $this->activityParticipant_01->canInitiate,
                    "canAttend" => $this->activityParticipant_01->canAttend,
                    "disabled" => $this->activityParticipant_01->disabled,
                    "feedbackForm" => [
                        "id" => $this->activityParticipant_01->feedbackForm->id,
                        "name" => $this->activityParticipant_01->feedbackForm->form->name,
                        "description" => $this->activityParticipant_01->feedbackForm->form->description,
                    ],
                ],
            ],
        ];
        
        $uri = $this->activityTypeUri . "/{$this->activityType->id}";
        $this->get($uri, $this->manager->token)
                ->seeJsonContains($reponse)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->activityType->id,
                    "name" => $this->activityType->name,
                    "disabled" => $this->activityType->disabled,
                ],
                [
                    "id" => $this->activityTypeOne->id,
                    "name" => $this->activityTypeOne->name,
                    "disabled" => $this->activityTypeOne->disabled,
                ],
            ],
        ];
        
        $this->get($this->activityTypeUri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
 