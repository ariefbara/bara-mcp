<?php

namespace Team\Domain\DependencyModel\Firm;

use Team\Domain\Model\Team;
use Tests\TestBase;

class ClientTest extends TestBase
{
    protected $client;
    
    protected $teamId = "teamId", $teamName = "team name", $memberPosition = "member position";

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new TestableClient();
        $this->client->activated = true;
    }
    
    protected function executeCreateTeam()
    {
        return $this->client->createTeam($this->teamId, $this->teamName, $this->memberPosition);
    }
    public function test_createTeam_returnTeam()
    {
        $this->assertInstanceOf(Team::class, $this->executeCreateTeam());
    }
    public function test_createTeam_inactiveClient_forbiddenError()
    {
        $this->client->activated = false;
        $operation = function (){
            $this->executeCreateTeam();
        };
        $errorDetail = "forbidden: only active client can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
}

class TestableClient extends Client
{
    public $firmId = 'firmId';
    public $id;
    public $activated;
    
    function __construct()
    {
        parent::__construct();
    }
}
