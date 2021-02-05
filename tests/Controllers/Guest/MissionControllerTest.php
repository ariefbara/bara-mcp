<?php

namespace Tests\Controllers\Guest;

use Tests\Controllers\ControllerTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\RecordOfFirm;

class MissionControllerTest extends ControllerTestCase
{
    protected $program;
    protected $missionOne;
    protected $missionTwo;
    protected $missionThree_unpublished;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Mission')->truncate();
        
        $firm = new RecordOfFirm('99');
        $this->connection->table('Firm')->insert($firm->toArrayForDbEntry());
        
        $this->program = new RecordOfProgram($firm, '99');
        $this->connection->table('Program')->insert($this->program->toArrayForDbEntry());
        
        $this->missionOne = new RecordOfMission($this->program, null, '1', null);
        $this->missionOne->published = true;
        $this->missionTwo = new RecordOfMission($this->program, null, '2', $this->missionOne);
        $this->missionTwo->published = true;
        $this->missionThree_unpublished = new RecordOfMission($this->program, null, '3', null);
        $this->connection->table('Mission')->insert($this->missionOne->toArrayForDbEntry());
        $this->connection->table('Mission')->insert($this->missionTwo->toArrayForDbEntry());
        $this->connection->table('Mission')->insert($this->missionThree_unpublished->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_show_200()
    {
        $uri = "/api/guest/missions/{$this->missionTwo->id}";
        $this->get($uri);
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->missionTwo->id,
            'name' => $this->missionTwo->name,
            'description' => $this->missionTwo->description,
            'position' => $this->missionTwo->position,
            'parent' => [
                'id' => $this->missionTwo->parent->id,
                'name' => $this->missionTwo->parent->name,
                'description' => $this->missionTwo->parent->description,
                'position' => $this->missionTwo->parent->position,
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    public function test_showAll_200()
    {
        $uri = "/api/guest/programs/{$this->program->id}/missions";
        $this->get($uri);
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);
        
        $missionOneResponse = [
            'id' => $this->missionOne->id,
            'name' => $this->missionOne->name,
            'description' => $this->missionOne->description,
            'position' => $this->missionOne->position,
            'parent' => null,
        ];
        $this->seeJsonContains($missionOneResponse);
        
        $missionTwoResponse = [
            'id' => $this->missionTwo->id,
            'name' => $this->missionTwo->name,
            'description' => $this->missionTwo->description,
            'position' => $this->missionTwo->position,
            'parent' => [
                'id' => $this->missionTwo->parent->id,
                'name' => $this->missionTwo->parent->name,
                'description' => $this->missionTwo->parent->description,
                'position' => $this->missionTwo->parent->position,
            ],
        ];
        $this->seeJsonContains($missionTwoResponse);
    }
}
