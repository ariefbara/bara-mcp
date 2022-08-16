<?php

namespace Payment\Domain\Model\Firm;

use SharedContext\Domain\ValueObject\CustomerInfo;
use Tests\TestBase;

class TeamTest extends TestBase
{
    protected $team;
    protected $creator, $creatorMail = 'creator@email.org';


    protected function setUp(): void
    {
        parent::setUp();
        $this->team = new TestableTeam();
        $this->creator = $this->buildMockOfClass(Client::class);
        $this->team->creator = $this->creator;
    }
    
    protected function generateCustomerInfo()
    {
        $this->creator->expects($this->any())
                ->method('getEmail')
                ->willReturn($this->creatorMail);
        return $this->team->generateCustomerInfo();
    }
    public function test_generateCustomerInfo_returnCustomerInfo()
    {
        $customerInfo = new CustomerInfo($this->team->name, $this->creatorMail);
        $this->assertEquals($customerInfo, $this->generateCustomerInfo());
    }
}

class TestableTeam extends Team
{
    public $id = 'teamId';
    public $name = 'team name';
    public $creator;
    
    function __construct()
    {
        parent::__construct();
    }
}
