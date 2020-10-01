<?php

namespace Tests\Controllers\Personnel\ProgramConsultation\Participant;

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
        
        $program = $this->programConsultation->program;
        
        $form = new RecordOfForm(1);
        $this->connection->table('Form')->insert($form->toArrayForDbEntry());
        
        $formRecord = new RecordOfFormRecord($form, 1);
        $this->connection->table('FormRecord')->insert($formRecord->toArrayForDbEntry());
        
        $worksheetForm = new RecordOfWorksheetForm($this->personnel->firm, $form);
        $this->connection->table('WorksheetForm')->insert($worksheetForm->toArrayForDbEntry());
        
        $mission = new RecordOfMission($program, $worksheetForm, 1, $this->worksheet->mission);
        $this->connection->table('Mission')->insert($mission->toArrayForDbEntry());

        $this->worksheetOne = new RecordOfWorksheet($this->participant, $formRecord, $mission, 1);
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
                "parent" => null,
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
        $this->get($uri, $this->programConsultation->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_show_removedConsultant_403()
    {
        $this->removeProgramConsultation();
        $uri = $this->worksheetUri . "/{$this->worksheetOne->id}";
        $this->get($uri, $this->programConsultation->personnel->token)
                ->seeStatusCode(403);
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
                    ],
                    "parent" => null,
                ],
                [
                    "id" => $this->worksheetOne->id,
                    "name" => $this->worksheetOne->name,
                    "mission" => [
                        "id" => $this->worksheetOne->mission->id,
                        "name" => $this->worksheetOne->mission->name,

                    ],
                    "parent" => [
                        "id" => $this->worksheetOne->parent->id,
                        "name" => $this->worksheetOne->parent->name,
                    ],
                ],
            ],
        ];
        $this->get($this->worksheetUri, $this->programConsultation->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
     }
     public function test_showall_removedConsultant_403()
     {
         $this->removeProgramConsultation();
        $this->get($this->worksheetUri, $this->programConsultation->personnel->token)
                ->seeStatusCode(403);
     }
     
     public function test_showAllRoot_200()
     {
        $response = [
            "total" => 1,
            "list" => [
                [
                    "id" => $this->worksheet->id,
                    "name" => $this->worksheet->name,
                    "mission" => [
                        "id" => $this->worksheet->mission->id,
                        "name" => $this->worksheet->mission->name,
                    ],
                    "parent" => null,
                ],
            ],
        ];
        $uri = $this->worksheetUri . "/roots";
        $this->get($uri, $this->programConsultation->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
     }
     public function test_showAllRoots_removedConsultant_403()
     {
        $this->removeProgramConsultation();
        $uri = $this->worksheetUri . "/roots";
        $this->get($uri, $this->programConsultation->personnel->token)
                ->seeStatusCode(403);
     }
     
     public function test_showAllBranches_200()
     {
$this->disableExceptionHandling();
         $response = [
            "total" => 1,
            "list" => [
                [
                    "id" => $this->worksheetOne->id,
                    "name" => $this->worksheetOne->name,
                    "mission" => [
                        "id" => $this->worksheetOne->mission->id,
                        "name" => $this->worksheetOne->mission->name,

                    ],
                    "parent" => [
                        "id" => $this->worksheetOne->parent->id,
                        "name" => $this->worksheetOne->parent->name,
                    ],
                ],
            ],
        ];
        $uri = $this->worksheetUri . "/{$this->worksheet->id}/branches";
        $this->get($uri, $this->programConsultation->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
     }
     public function test_showAllBranches_removedConsultant_403()
     {
         $this->removeProgramConsultation();
        $uri = $this->worksheetUri . "/{$this->worksheet->id}/branches";
        $this->get($uri, $this->programConsultation->personnel->token)
                ->seeStatusCode(403);
     }
}
