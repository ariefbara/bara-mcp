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
    /**
     *
     * @var RecordOfMember
     */
    protected $teamMembershipOne_otherMember;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connection->table("Team")->truncate();
        $this->connection->table("T_Member")->truncate();
        
        $firm = $this->client->firm;
        
        $team = new RecordOfTeam($firm, $this->client, 0);
        $this->connection->table("Team")->insert($team->toArrayForDbEntry());
        
        $client = new RecordOfClient($firm, 1);
        $client->email = "go.on.apur@gmail.com";
        $this->connection->table("Client")->insert($client->toArrayForDbEntry());
        
        $this->teamMembership = new RecordOfMember($team, $this->client, 0);
        $this->teamMembershipOne_otherMember = new RecordOfMember($team, $client, 1);
        $this->connection->table("T_Member")->insert($this->teamMembership->toArrayForDbEntry());
        $this->connection->table("T_Member")->insert($this->teamMembershipOne_otherMember->toArrayForDbEntry());
        
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
