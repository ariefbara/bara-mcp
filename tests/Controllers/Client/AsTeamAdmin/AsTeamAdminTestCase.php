<?php

namespace Tests\Controllers\Client\AsTeamAdmin;

use Tests\Controllers\ {
    Client\ClientTestCase,
    RecordPreparation\Firm\RecordOfClient,
    RecordPreparation\Firm\RecordOfTeam,
    RecordPreparation\Firm\Team\RecordOfMember
};

class AsTeamAdminTestCase extends ClientTestCase
{
    protected $asTeamAdminUri;
    /**
     *
     * @var RecordOfMember
     */
    protected $memberWithAdminPriviledge;
    /**
     *
     * @var RecordOfMember
     */
    protected $memberWithoutAdminPriviledge;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table("Team")->truncate();
        $this->connection->table("T_Member")->truncate();
        
        $firm = $this->client->firm;
        
        $client = new RecordOfClient($firm, "nonAdminTeamMember");
        $this->connection->table("Client")->insert($client->toArrayForDbEntry());
        
        $team = new RecordOfTeam($firm, $this->client, 0);
        $this->connection->table("Team")->insert($team->toArrayForDbEntry());
        
        $this->memberWithAdminPriviledge = new RecordOfMember($team, $this->client, "admin");
        $this->memberWithoutAdminPriviledge = new RecordOfMember($team, $client, "non-admin");
        $this->memberWithoutAdminPriviledge->anAdmin = false;
        $this->connection->table("T_Member")->insert($this->memberWithAdminPriviledge->toArrayForDbEntry());
        $this->connection->table("T_Member")->insert($this->memberWithoutAdminPriviledge->toArrayForDbEntry());
        
        $this->asTeamAdminUri = $this->clientUri . "/as-team-admin/{$team->id}";
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Team")->truncate();
        $this->connection->table("T_Member")->truncate();
    }
}
