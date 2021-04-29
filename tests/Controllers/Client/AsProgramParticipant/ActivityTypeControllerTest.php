<?php

namespace Tests\Controllers\Client\AsProgramParticipant;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\ActivityType\RecordOfActivityParticipant,
    Firm\Program\RecordOfActivityType,
    Firm\RecordOfFeedbackForm,
    Shared\RecordOfForm
};

class ActivityTypeControllerTest extends AsProgramParticipantTestCase
{
    protected $feedbackForm;
    protected $activityTypeUri;
    protected $activityType;
    protected $activityTypeOne;
    protected $activityParticipant_00;
    protected $activityParticipant_01;
    protected $activityParticipant_10;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activityTypeUri = $this->asProgramParticipantUri . "/activity-types";
        
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        
        $program = $this->programParticipation->participant->program;
        $firm = $program->firm;
        
        $this->activityType = new RecordOfActivityType($program, 0);
        $this->activityTypeOne = new RecordOfActivityType($program, 1);
        $this->connection->table("ActivityType")->insert($this->activityType->toArrayForDbEntry());
        $this->connection->table("ActivityType")->insert($this->activityTypeOne->toArrayForDbEntry());
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $this->feedbackForm = new RecordOfFeedbackForm($firm, $form);
        $this->connection->table("FeedbackForm")->insert($this->feedbackForm->toArrayForDbEntry());
        
        $this->activityParticipant_00 = new RecordOfActivityParticipant($this->activityType, $this->feedbackForm, "00");
        $this->activityParticipant_01 = new RecordOfActivityParticipant($this->activityType, $this->feedbackForm, "01");
        $this->activityParticipant_01->participantType = "participant";
        $this->activityParticipant_10 = new RecordOfActivityParticipant($this->activityTypeOne, $this->feedbackForm, "10");
        $this->connection->table("ActivityParticipant")->insert($this->activityParticipant_00->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($this->activityParticipant_01->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($this->activityParticipant_10->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
    }
    
    public function test_show_200()
    {
        $reponse = [
            "id" => $this->activityType->id,
            "name" => $this->activityType->name,
            "description" => $this->activityType->description,
            "participants" => [
                [
                    "id" => $this->activityParticipant_00->id,
                    "participantType" => $this->activityParticipant_00->participantType,
                    "canInitiate" => $this->activityParticipant_00->canInitiate,
                    "canAttend" => $this->activityParticipant_00->canAttend,
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
                    "feedbackForm" => [
                        "id" => $this->activityParticipant_01->feedbackForm->id,
                        "name" => $this->activityParticipant_01->feedbackForm->form->name,
                        "description" => $this->activityParticipant_01->feedbackForm->form->description,
                    ],
                ],
            ],
        ];
        
        $uri = $this->activityTypeUri . "/{$this->activityType->id}";
        $this->get($uri, $this->programParticipation->client->token)
                ->seeJsonContains($reponse)
                ->seeStatusCode(200);
    }
    public function test_show_inactiveParticipant_403()
    {
        $uri = $this->activityTypeUri . "/{$this->activityType->id}";
        $this->get($uri, $this->inactiveProgramParticipation->client->token)
                ->seeStatusCode(403);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 1,
            "list" => [
                [
                    "id" => $this->activityType->id,
                    "name" => $this->activityType->name,
                ],
            ],
        ];
        
        $this->get($this->activityTypeUri, $this->programParticipation->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveParticipant_403()
    {
        $this->get($this->activityTypeUri, $this->inactiveProgramParticipation->client->token)
                ->seeStatusCode(403);
    }
}
 