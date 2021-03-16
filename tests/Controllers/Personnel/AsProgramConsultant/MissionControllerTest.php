<?php

namespace Tests\Controllers\Personnel\AsProgramConsultant;

use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\RecordOfWorksheetForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class MissionControllerTest extends AsProgramConsultantTestCase
{
    protected $missionUri;
    protected $missionOne;
    protected $missionTwo;
    protected $missionThree_unpublished;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->missionUri = $this->asProgramConsultantUri . "/missions";
        $this->connection->table("Form")->truncate();
        $this->connection->table("WorksheetForm")->truncate();
        $this->connection->table("Mission")->truncate();
        
        $program = $this->consultant->program;
        $firm = $program->firm;
        
        $formOne = new RecordOfForm(1);
        $formTwo = new RecordOfForm(2);
        $formThree = new RecordOfForm(3);
        $this->connection->table("Form")->insert($formOne->toArrayForDbEntry());
        $this->connection->table("Form")->insert($formTwo->toArrayForDbEntry());
        $this->connection->table("Form")->insert($formThree->toArrayForDbEntry());
        
        $worksheetFormOne = new RecordOfWorksheetForm($firm, $formOne);
        $worksheetFormTwo = new RecordOfWorksheetForm($firm, $formTwo);
        $worksheetFormThree = new RecordOfWorksheetForm($firm, $formThree);
        $this->connection->table("WorksheetForm")->insert($worksheetFormOne->toArrayForDbEntry());
        $this->connection->table("WorksheetForm")->insert($worksheetFormTwo->toArrayForDbEntry());
        $this->connection->table("WorksheetForm")->insert($worksheetFormThree->toArrayForDbEntry());
        
        $this->missionOne = new RecordOfMission($program, $worksheetFormOne, 1, null);
        $this->missionOne->published = true;
        $this->missionTwo = new RecordOfMission($program, $worksheetFormTwo, 2, $this->missionOne);
        $this->missionTwo->published = true;
        $this->missionThree_unpublished = new RecordOfMission($program, $worksheetFormThree, 3, null);
        $this->missionThree_unpublished->published = false;
        $this->connection->table("Mission")->insert($this->missionOne->toArrayForDbEntry());
        $this->connection->table("Mission")->insert($this->missionTwo->toArrayForDbEntry());
        $this->connection->table("Mission")->insert($this->missionThree_unpublished->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Form")->truncate();
        $this->connection->table("WorksheetForm")->truncate();
        $this->connection->table("Mission")->truncate();
    }
    
    public function test_showAll_200()
    {
        $this->get($this->missionUri, $this->consultant->personnel->token)
                ->seeStatusCode(200);
        
        $totalResponse = ["total" => 2];
        $missionOneReponse = [
            "id" => $this->missionOne->id,
            "name" => $this->missionOne->name,
            "position" => $this->missionOne->position,
            "worksheetForm" => [
                "id" => $this->missionOne->worksheetForm->id,
                "name" => $this->missionOne->worksheetForm->form->name,
            ],
        ];
        $this->seeJsonContains($missionOneReponse);
        $missionTwoReponse = [
            "id" => $this->missionTwo->id,
            "name" => $this->missionTwo->name,
            "position" => $this->missionTwo->position,
            "worksheetForm" => [
                "id" => $this->missionTwo->worksheetForm->id,
                "name" => $this->missionTwo->worksheetForm->form->name,
            ],
        ];
        $this->seeJsonContains($missionTwoReponse);
    }
    
    public function test_show_200()
    {
        $uri = $this->missionUri . "/{$this->missionTwo->id}";
        $this->get($uri, $this->consultant->personnel->token)
                ->seeStatusCode(200);
        
        $response = [
            "id" => $this->missionTwo->id,
            "name" => $this->missionTwo->name,
            "description" => $this->missionTwo->description,
            "position" => $this->missionTwo->position,
            "worksheetForm" => [
                "id" => $this->missionTwo->worksheetForm->id,
                "name" => $this->missionTwo->worksheetForm->form->name,
            ],
            "parent" => [
                "id" => $this->missionTwo->parent->id,
                "name" => $this->missionTwo->parent->name,
            ],
        ];
        $this->seeJsonContains($response);
    }
}
