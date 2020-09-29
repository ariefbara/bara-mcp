<?php

namespace Query\Domain\Model\Firm\Team;

use Query\Domain\ {
    Model\Firm\Client,
    Service\Firm\ClientFinder
};
use Tests\TestBase;

class MemberTest extends TestBase
{
    protected $member;

    protected $clientFinder, $clientEmail = "client@email.org";

    protected function setUp(): void
    {
        parent::setUp();
        $this->member = new TestableMember();
        $this->member->client = $this->buildMockOfClass(Client::class);
        
        $this->clientFinder = $this->buildMockOfClass(ClientFinder::class);
    }
    protected function assertNotAdminForbiddenError(callable $operation): void
    {
        $errorDetail = "forbidden: only team admin can make this requests";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    protected function assertInactiveForbiddenError(callable $operation): void
    {
        $errorDetail = "forbidden: only active team member can make this requests";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeViewClientByEmail()
    {
        return $this->member->viewClientByEmail($this->clientFinder, $this->clientEmail);
    }
    public function test_viewClientByEmail_returnClientFindersFindByEmailResult()
    {
        $this->clientFinder->expects($this->once())
                ->method("findByEmail")
                ->with($this->anything(), $this->clientEmail);
        $this->executeViewClientByEmail();
    }
    public function test_viewClientByEmail_notAdmin_forbiddenError()
    {
        $this->member->anAdmin = false;
        $this->assertNotAdminForbiddenError(function (){
            $this->executeViewClientByEmail();
        });
    }
    public function test_viewClientByEmail_inactiveMember_forbiddenError()
    {
        $this->member->active = false;
        $this->assertInactiveForbiddenError(function (){
            $this->executeViewClientByEmail();
        });
    }
}

class TestableMember extends Member
{
    public $team;
    public $id;
    public $client;
    public $position;
    public $anAdmin = true;
    public $active = true;
    public $joinTime;
    
    function __construct()
    {
        parent::__construct();
    }
}
