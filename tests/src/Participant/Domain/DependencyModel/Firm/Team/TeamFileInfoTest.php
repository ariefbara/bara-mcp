<?php

namespace Participant\Domain\DependencyModel\Firm\Team;

use Participant\Domain\DependencyModel\Firm\Team;
use Tests\TestBase;

class TeamFileInfoTest extends TestBase
{
    protected $teamFileInfo;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamFileInfo = new TestableTeamFileInfo();
        $this->teamFileInfo->team = $this->buildMockOfClass(Team::class);
    }
    
    public function test_belongsToTeam_sameTeam_returnTrue()
    {
        $this->assertTrue($this->teamFileInfo->belongsToTeam($this->teamFileInfo->team));
    }
    public function test_belongsToTeam_differentTeam_returnFalse()
    {
        $team = $this->buildMockOfClass(Team::class);
        $this->assertFalse($this->teamFileInfo->belongsToTeam($team));
    }
}

class TestableTeamFileInfo extends TeamFileInfo
{
    public $team;
    public $id;
    public $fileInfo;
    
    function __construct()
    {
        parent::__construct();
    }
}
