<?php

namespace Tests\Controllers\Personnel\AsProgramConsultant\Mission;

use Tests\Controllers\Personnel\AsProgramConsultant\AsProgramConsultantTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;

class MissionTestCase extends AsProgramConsultantTestCase
{
    protected $missionUri;
    /**
     * 
     * @var RecordOfMission
     */
    protected $mission;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $program = $this->consultant->program;
        
        $this->mission = new RecordOfMission($program, null, '99', null);
        $this->connection->table('Mission')->truncate();
        
        $this->missionUri = $this->asProgramConsultantUri . "/missions/{$this->mission->id}";
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Mission')->truncate();
    }
    
    protected function insertAggregateEntry(): void
    {
        $this->mission->insert($this->connection);
    }
}
