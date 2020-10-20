<?php

namespace Tests\Controllers\Client\AsTeamMember;

use Tests\Controllers\ {
    Client\ClientTestCase,
    RecordPreparation\Firm\RecordOfClient,
    RecordPreparation\Firm\RecordOfTeam,
    RecordPreparation\Firm\Team\RecordOfMember,
    RecordPreparation\RecordOfFirm
};

class AsTeamMemberTestCase extends ClientTestCase
{
    protected $asTeamMemberUri;
    /**
     *
     * @var RecordOfMember
     */
    protected $teamMember;
    /**
     *
     * @var RecordOfMember
     */
    protected $teamMemberOne_inactive;
    /**
     *
     * @var RecordOfMember
     */
    protected $teamMemberTwo_notAdmin;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table("Team")->truncate();
        $this->connection->table("T_Member")->truncate();
        
        $firm = $this->client->firm;
        
        $client = new RecordOfClient($firm, "inactiveMember");
        $clientOne = new RecordOfClient($firm, "nonAdmin");
        $this->connection->table("Client")->insert($client->toArrayForDbEntry());
        $this->connection->table("Client")->insert($clientOne->toArrayForDbEntry());
        
        $team = new RecordOfTeam($firm, $this->client, 999);
        $this->connection->table("Team")->insert($team->toArrayForDbEntry());
        
        
        $this->teamMember = new RecordOfMember($team, $this->client, 999);
        $this->teamMemberOne_inactive = new RecordOfMember($team, $client, 998);
        $this->teamMemberOne_inactive->active = false;
        $this->teamMemberTwo_notAdmin = new RecordOfMember($team, $clientOne, 997);
        $this->teamMemberTwo_notAdmin->anAdmin = false;
        $this->connection->table("T_Member")->insert($this->teamMember->toArrayForDbEntry());
        $this->connection->table("T_Member")->insert($this->teamMemberOne_inactive->toArrayForDbEntry());
        $this->connection->table("T_Member")->insert($this->teamMemberTwo_notAdmin->toArrayForDbEntry());
        
        $this->asTeamMemberUri = $this->clientUri . "/as-team-member/{$team->id}";
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Team")->truncate();
        $this->connection->table("T_Member")->truncate();
    }
}
