<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator\Participant;

use Tests\Controllers\ {
    Personnel\AsProgramCoordinator\ParticipantTestCase,
    RecordPreparation\Firm\Program\Participant\RecordOfWorksheet,
    RecordPreparation\Firm\Program\RecordOfMission,
    RecordPreparation\Firm\RecordOfWorksheetForm,
    RecordPreparation\Shared\RecordOfForm,
    RecordPreparation\Shared\RecordOfFormRecord
};

class WorksheetControllerTest extends ParticipantTestCase
{
    protected $worksheetUri;
    protected $worksheet;
    protected $worksheetOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheetUri = $this->participantUri . "/{$this->participant->id}/worksheets";
        
        $this->connection->table("Form")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("WorksheetForm")->truncate();
        $this->connection->table("Mission")->truncate();
        $this->connection->table("Worksheet")->truncate();
        
        $program = $this->coordinator->program;
        $firm = $program->firm;
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $formRecord = new RecordOfFormRecord($form, 0);
        $formRecordOne = new RecordOfFormRecord($form, 1);
        $this->connection->table("FormRecord")->insert($formRecord->toArrayForDbEntry());
        $this->connection->table("FormRecord")->insert($formRecordOne->toArrayForDbEntry());
        
        $worksheetForm = new RecordOfWorksheetForm($firm, $form);
        $this->connection->table("WorksheetForm")->insert($worksheetForm->toArrayForDbEntry());
        
        $mission = new RecordOfMission($program, $worksheetForm, 0, null);
        $missionOne = new RecordOfMission($program, $worksheetForm, 1, $mission);
        $this->connection->table("Mission")->insert($mission->toArrayForDbEntry());
        $this->connection->table("Mission")->insert($missionOne->toArrayForDbEntry());
        
        $this->worksheet = new RecordOfWorksheet($this->participant, $formRecord, $mission, 0);
        $this->worksheetOne = new RecordOfWorksheet($this->participant, $formRecordOne, $missionOne, 1);
        $this->worksheetOne->parent = $this->worksheet;
        $this->connection->table("Worksheet")->insert($this->worksheet->toArrayForDbEntry());
        $this->connection->table("Worksheet")->insert($this->worksheetOne->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("WorksheetForm")->truncate();
        $this->connection->table("Mission")->truncate();
        $this->connection->table("Worksheet")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->worksheetOne->id,
            "name" => $this->worksheetOne->name,
            "mission" => [
                "id" => $this->worksheetOne->mission->id,
                "name" => $this->worksheetOne->mission->name,
                "description" => $this->worksheetOne->mission->description,
                "worksheetForm" => [
                    "id" => $this->worksheetOne->mission->worksheetForm->id,
                    "name" => $this->worksheetOne->mission->worksheetForm->form->name,
                    "description" => $this->worksheetOne->mission->worksheetForm->form->description,
                ],
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
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_show_inactiveCoordinator_403()
    {
        $uri = $this->worksheetUri . "/{$this->worksheetOne->id}";
        $this->get($uri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_showAll_200()
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
        $this->get($this->worksheetUri, $this->coordinator->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_inactiveCoordinator_403()
    {
        $this->get($this->worksheetUri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }
    public function test_showAll_hasParentFilterSet_200()
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
                        "description" => $this->worksheet->mission->description,

                    ],
                    "parent" => null,
                ],
            ],
        ];
        $uri = $this->worksheetUri . "?hasParent=false";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_missionIdFilterSet_200()
    {
        $response = [
            "total" => 1,
            "list" => [
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
        $uri = $this->worksheetUri . "?missionId={$this->worksheetOne->mission->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_parentIdFilterSet_200()
    {
        $response = [
            "total" => 1,
            "list" => [
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
        $uri = $this->worksheetUri . "?parentId={$this->worksheet->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
