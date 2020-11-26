<?php

namespace Firm\Domain\Model\Firm;

class TeamTest extends \Tests\TestBase
{
    protected $team;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->team = new TestableTeam();
    }
    
    public function test_idEquals_sameId_returnTrue()
    {
        $this->assertTrue($this->team->idEquals($this->team->id));
    }
    public function test_idEquals_differentId_returnFalse()
    {
        $this->assertFalse($this->team->idEquals("differentId"));
    }
}

class TestableTeam extends Team
{
     public $firm;
     public $id = "id";
     
     function __construct()
     {
         parent::__construct();
     }
}
