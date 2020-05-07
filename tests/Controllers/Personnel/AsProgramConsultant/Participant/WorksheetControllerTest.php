<?php

namespace Tests\Controllers\Personnel\AsProgramConsultant\Participant;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Participant\RecordOfWorksheet,
    Firm\Program\RecordOfMission,
    Firm\RecordOfWorksheetForm,
    Shared\RecordOfForm,
    Shared\RecordOfFormRecord
};

class WorksheetControllerTest extends WorksheetTestCase
{
    protected $worksheetOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $form = new RecordOfForm(1);
        $this->connection->table('Form')->insert($form->toArrayForDbEntry());
        
        $formRecord = new RecordOfFormRecord($form, 1);
        $this->connection->table('FormRecord')->insert($formRecord->toArrayForDbEntry());
        
        $worksheetForm = new RecordOfWorksheetForm($this->personnel->firm, $form);
        $this->connection->table('WorksheetForm')->insert($worksheetForm->toArrayForDbEntry());
        
        $mission = new RecordOfMission($this->consultant->program, $worksheetForm, 1, $this->worksheet->mission);
        $this->connection->table('Mission')->insert($mission->toArrayForDbEntry());

        $this->worksheetOne = new RecordOfWorksheet($this->participant, $formRecord, $mission);
        $this->worksheetOne->parent = $this->worksheet;
        $this->connection->table('Worksheet')->insert($this->worksheetOne->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->worksheetOne->id,
            "name" => $this->worksheetOne->name,
            "mission" => [
                "id" => $this->worksheetOne->mission->id,
                "name" => $this->worksheetOne->mission->name,
                "description" => $this->worksheetOne->mission->description,
                
            ],
            "parent" => [
                "id" => $this->worksheetOne->parent->id,
                "name" => $this->worksheetOne->parent->name,
            ],
            "submitTime" => $this->worksheetOne->formRecord->submitTime,
            "stringFieldRecords" => [],
            "integerFieldRecords" => [],
            "textAreaFieldRecords" => [],
            "attachmentFieldRecords" => [],
            "singleSelectFieldRecords" => [],
            "multiSelectFieldRecords" => [],
        ];
        $uri = $this->worksheetUri . "/{$this->worksheetOne->id}";
        $this->get($uri, $this->consultant->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_show_personnelNotProgramConsultant()
    {
        $uri = $this->worksheetUri . "/{$this->worksheetOne->id}";
        $this->get($uri, $this->removedConsultant->personnel->token)
                ->seeStatusCode(401);
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->worksheet->id,
                    "name" => $this->worksheet->name,
                    "mission" => [
                        "id" => $this->worksheet->mission->id,
                        "name" => $this->worksheet->mission->name,
                        "description" => $this->worksheet->mission->description,

                    ],
                    "parent" => null,
                ],
                [
                    "id" => $this->worksheetOne->id,
                    "name" => $this->worksheetOne->name,
                    "mission" => [
                        "id" => $this->worksheetOne->mission->id,
                        "name" => $this->worksheetOne->mission->name,
                        "description" => $this->worksheetOne->mission->description,

                    ],
                    "parent" => [
                        "id" => $this->worksheetOne->parent->id,
                        "name" => $this->worksheetOne->parent->name,
                    ],
                ],
            ],
        ];
        $this->get($this->worksheetUri, $this->consultant->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
     }
     public function test_showAll_personnelNotProgramConsultant_error401()
     {
        $this->get($this->worksheetUri, $this->removedConsultant->personnel->token)
                ->seeStatusCode(401);
         
     }
}
