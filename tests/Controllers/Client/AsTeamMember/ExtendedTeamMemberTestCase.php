<?php

namespace Tests\Controllers\Client\AsTeamMember;

use Tests\Controllers\Client\ClientTestCase;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;

class ExtendedTeamMemberTestCase extends ClientTestCase
{
    /**
     * 
     * @var RecordOfMember
     */
    protected $teamMember;
    protected $asTeamMemberUri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        
        $firm = $this->client->firm;
        
        $team = new RecordOfTeam($firm, $this->client, '999');
        $this->teamMember = new RecordOfMember($team, $this->client, '999');
        
        $this->asTeamMemberUri = $this->clientUri . "/as-team-member/{$team->id}";
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
    }
    
    protected function prepareRecord(): void
    {
        $this->teamMember->team->insert($this->connection);
        $this->teamMember->insert($this->connection);
    }
}
