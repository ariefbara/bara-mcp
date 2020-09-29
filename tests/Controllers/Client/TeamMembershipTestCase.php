<?php

namespace Tests\Controllers\Client;

use Tests\Controllers\RecordPreparation\Firm\ {
    RecordOfClient,
    RecordOfTeam,
    Team\RecordOfMember
};

class TeamMembershipTestCase extends ClientTestCase
{
    protected $teamMembershipUri;
    /**
     *
     * @var RecordOfMember
     */
    protected $teamMembership;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connection->table("Team")->truncate();
        $this->connection->table("T_Member")->truncate();
        
        $firm = $this->client->firm;
        
        $team = new RecordOfTeam($firm, $this->client, 0);
        $this->connection->table("Team")->insert($team->toArrayForDbEntry());
        
        
        $this->teamMembership = new RecordOfMember($team, $this->client, 0);
        $this->connection->table("T_Member")->insert($this->teamMembership->toArrayForDbEntry());
        
        $this->teamMembershipUri = $this->clientUri . "/team-memberships/{$this->teamMembership->id}";
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Team")->truncate();
        $this->connection->table("T_Member")->truncate();
    }
    protected function setTeamMembershipNotAnAdmin(): void
    {
        $this->connection->table("T_Member")->truncate();
        $this->teamMembership->anAdmin = false;
        $this->connection->table("T_Member")->insert($this->teamMembership->toArrayForDbEntry());
    }
    protected function setTeamMembershipInactive(): void
    {
        $this->connection->table("T_Member")->truncate();
        $this->teamMembership->active = false;
        $this->connection->table("T_Member")->insert($this->teamMembership->toArrayForDbEntry());
    }
}
