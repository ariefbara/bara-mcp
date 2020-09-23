<?php

namespace Client\Domain\Model\Client;

use Tests\TestBase;

class TeamMembershipTest extends TestBase
{
    protected $teamMembership;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamMembership = new TestableTeamMembership();
    }
    protected function executeQuit()
    {
        $this->teamMembership->quit();
    }
    public function test_quit_setActiveFalse()
    {
        $this->executeQuit();
        $this->assertFalse($this->teamMembership->active);
    }
    public function test_quit_alreadyInactive_forbiddenError()
    {
        $this->teamMembership->active = false;
        $operation = function (){
            $this->executeQuit();
        };
        $errorDetail = "forbidden: already inactive member";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
}

class TestableTeamMembership extends TeamMembership
{
    public $client;
    public $id;
    public $active = true;
    
    function __construct()
    {
        parent::__construct();
    }
}
