<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator\Mission;

use Tests\Controllers\Personnel\AsProgramCoordinator\AsProgramCoordinatorTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;

class MissionTestCase extends AsProgramCoordinatorTestCase
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
        
        $program = $this->coordinator->program;
        
        $this->mission = new RecordOfMission($program, null, '99', null);
        $this->connection->table('Mission')->truncate();
        
        $this->missionUri = $this->asProgramCoordinatorUri . "/missions/{$this->mission->id}";
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
