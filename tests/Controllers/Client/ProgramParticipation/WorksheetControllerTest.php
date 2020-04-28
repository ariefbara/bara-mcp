<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use DateTime;
use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Participant\RecordOfWorksheet,
    Firm\Program\RecordOfMission,
    Shared\RecordOfFormRecord
};

class WorksheetControllerTest extends WorksheetTestCase
{
    protected $worksheetOne;
    protected $branchMission;

    protected function setUp(): void
    {
        parent::setUp();
        $this->branchMission = new RecordOfMission($this->programParticipation->program, $this->worksheetForm, 1, $this->mission);
        $this->connection->table('Mission')->insert($this->branchMission->toArrayForDbEntry());
        
        $formRecord = new RecordOfFormRecord($this->form, 1);
        $this->connection->table('FormRecord')->insert($formRecord->toArrayForDbEntry());
        
        $this->worksheetOne = new RecordOfWorksheet($this->programParticipation, $formRecord, $this->mission);
        $this->connection->table('Worksheet')->insert($this->worksheetOne->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_addRoot()
    {
        $this->connection->table('Worksheet')->truncate();
        $this->connection->table('FormRecord')->truncate();
        
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(201)
                ->seeJsonContains($this->worksheetResponse);
        
        $worksheetEntry = [
            "parent_id" => null,
            "name" => $this->worksheetInput['name'],
            "Participant_id" => $this->programParticipation->id,
            "removed" => false,
        ];
        $this->seeInDatabase('Worksheet', $worksheetEntry);
        
        $formRecordEntry = [
            "submitTime" => (new DateTime())->format("Y-m-d H:i:s"),
            "Form_id" => $this->form->id,
        ];
        $this->seeInDatabase('FormRecord', $formRecordEntry);
    }
    public function test_addRoot_missionNotRootMission_error403()
    {
        $this->worksheetInput['missionId'] = $this->branchMission->id;
        
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(403);
    }
    public function test_addRoot_emptyName_error400()
    {
        $this->worksheetInput['name'] = "";
        
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(400);
    }
    
    public function test_addBranch()
    {
        $this->worksheetInput['missionId'] = $this->branchMission->id;
        $this->worksheetResponse['parent'] = [
            "id" => $this->worksheet->id,
            "name" => $this->worksheet->name,
        ];
        $this->worksheetResponse['mission'] = [
            "id" => $this->branchMission->id,
            "name" => $this->branchMission->name,
            "worksheetForm" => [
                "id" => $this->branchMission->worksheetForm->id,
            ],
        ];
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->post($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(201)
                ->seeJsonContains($this->worksheetResponse);
        
        $worksheetEntry = [
            "parent_id" => $this->worksheet->id,
            "name" => $this->worksheetInput['name'],
            "Participant_id" => $this->programParticipation->id,
            "removed" => false,
        ];
        $this->seeInDatabase('Worksheet', $worksheetEntry);
        
        $formRecordEntry = [
            "submitTime" => (new DateTime())->format("Y-m-d H:i:s"),
            "Form_id" => $this->form->id,
        ];
        $this->seeInDatabase('FormRecord', $formRecordEntry);
    }
    public function test_addBranch_emptyName_error400()
    {
        $this->worksheetInput['missionId'] = $this->branchMission->id;
        $this->worksheetInput['name'] = "";
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->post($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(400);
    }
    public function test_addBranch_missionNotBranchOfParentWorksheetMission_error403()
    {
        $mission = new RecordOfMission($this->programParticipation->program, $this->worksheetForm, 2, null);
        $this->connection->table('Mission')->insert($mission->toArrayForDbEntry());
        
        $this->worksheetInput["missionId"] = $mission->id;
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->post($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(403);
    }
    
    public function test_update()
    {
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($this->worksheetResponse);
        
        $worksheetEntry = [
            "id" => $this->worksheet->id,
            "parent_id" => null,
            "name" => $this->worksheetInput['name'],
            "Participant_id" => $this->programParticipation->id,
            "removed" => false,
        ];
        $this->seeInDatabase('Worksheet', $worksheetEntry);
    }
    public function test_update_emptyName_error400()
    {
        $this->worksheetInput['name'] = "";
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(400);
    }
    
    public function test_remove()
    {
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->delete($uri, [], $this->client->token)
                ->seeStatusCode(200);
        
        $worksheetEntry = [
            "id" => $this->worksheet->id,
            "removed" => true,
        ];
        $this->seeInDatabase('Worksheet', $worksheetEntry);
    }
    
    public function test_show()
    {
        $this->worksheetResponse['id'] = $this->worksheet->id;
        $this->worksheetResponse['name'] = $this->worksheet->name;
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->get($uri, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($this->worksheetResponse);
    }
    public function test_showAll()
    {
        $response = [
            "total" => 2, 
            "list" => [
                [
                    "id" => $this->worksheet->id,
                    "name" => $this->worksheet->name,
                ],
                [
                    "id" => $this->worksheetOne->id,
                    "name" => $this->worksheetOne->name,
                ],
            ],
        ];
        $this->get($this->worksheetUri, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    
}
