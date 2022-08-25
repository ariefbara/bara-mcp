<?php

namespace Tests\Controllers\Client\AsTeamMember;

use Tests\Controllers\ControllerTestCase;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;
use Tests\Controllers\RecordPreparation\RecordOfFirm;

class ExtendedAsTeamMemberTestCase extends ControllerTestCase
{
    /**
     * 
     * @var RecordOfMember
     */
    protected $teamMember;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        
        $firm = new RecordOfFirm('0');
        
        $client = new RecordOfClient($firm, '0');
        
        $team = new RecordOfTeam($firm, $client, '0');
        
        $this->teamMember = new RecordOfMember($team, $client, '0');
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
    }
    
    protected function persistTeamMemberDependency()
    {
        $this->teamMember->client->firm->insert($this->connection);
        $this->teamMember->client->insert($this->connection);
        $this->teamMember->team->insert($this->connection);
        $this->teamMember->insert($this->connection);
    }
}
