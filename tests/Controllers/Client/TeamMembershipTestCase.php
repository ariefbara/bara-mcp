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
    protected $inactiveTeamMembership;
    /**
     *
     * @var RecordOfMember
     */
    protected $otherTeamMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamMembershipUri = $this->clientUri . "/team-memberships";
        
        $this->connection->table("Team")->truncate();
        $this->connection->table("T_Member")->truncate();
        
        $firm = $this->client->firm;
        
        $client_otherTeamMember = new RecordOfClient($firm, "otherTeamCreator");
        $client_inactiveMember = new RecordOfClient($firm, 'inactiveTeamMember');
        $this->connection->table("Client")->insert($client_otherTeamMember->toArrayForDbEntry());
        $this->connection->table("Client")->insert($client_inactiveMember->toArrayForDbEntry());
        
        $team = new RecordOfTeam($firm, $this->client, 0);
        $otherTeam = new RecordOfTeam($firm, $client_otherTeamMember, "otherTeam");
        $this->connection->table("Team")->insert($team->toArrayForDbEntry());
        $this->connection->table("Team")->insert($otherTeam->toArrayForDbEntry());
        
        
        $this->teamMembership = new RecordOfMember($team, $this->client, 0);
        $this->otherTeamMember = new RecordOfMember($otherTeam, $client_otherTeamMember, "otherTeamMember");
        $this->inactiveTeamMembership = new RecordOfMember($team, $client_inactiveMember, "inactive");
        $this->inactiveTeamMembership->active = false;
        $this->connection->table("T_Member")->insert($this->teamMembership->toArrayForDbEntry());
        $this->connection->table("T_Member")->insert($this->otherTeamMember->toArrayForDbEntry());
        $this->connection->table("T_Member")->insert($this->inactiveTeamMembership->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Team")->truncate();
        $this->connection->table("T_Member")->truncate();
    }
}
