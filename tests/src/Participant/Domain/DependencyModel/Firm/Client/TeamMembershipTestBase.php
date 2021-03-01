<?php

namespace Tests\src\Participant\Domain\DependencyModel\Firm\Client;

use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\DependencyModel\Firm\Team;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class TeamMembershipTestBase extends TestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $team;
    /**
     * 
     * @var TestableTeamMembership
     */
    protected $teamMembership;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamMembership = new TestableTeamMembership();
        $this->team = $this->buildMockOfClass(Team::class);
        $this->teamMembership->team = $this->team;
    }
    protected function assertInactiveTeamMemberError(callable $operation): void
    {
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: only active team member can make this request');
    }
    protected function assertAssetUnmanageableError(callable $operation): void
    {
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: you can only manage asset belongs to your team');
    }
    protected function setAssetBelongsToTeam(MockObject $asset): void
    {
        $asset->expects($this->any())
                ->method('belongsToTeam')
                ->with($this->team)
                ->willReturn(true);
    }
    protected function setAssetNotBelongsToTeam(MockObject $asset): void
    {
        $asset->expects($this->any())
                ->method('belongsToTeam')
                ->with($this->team)
                ->willReturn(false);
    }

}

class TestableTeamMembership extends TeamMembership
{
    public $client;
    public $id = 'memberId';
    public $team;
    public $active = true;
    
    function __construct()
    {
        parent::__construct();
    }
}
