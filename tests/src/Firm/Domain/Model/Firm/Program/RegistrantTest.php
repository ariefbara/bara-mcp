<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program;
use Query\Domain\Model\Client;
use Tests\TestBase;

class RegistrantTest extends TestBase
{

    protected $program, $client;
    protected $registrant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->client = $this->buildMockOfClass(Client::class);

        $this->registrant = new TestableRegistrant();
        $this->registrant->program = $this->program;
        $this->registrant->client = $this->client;
    }

    protected function executeAccept()
    {
        $this->registrant->accept();
    }

    public function test_accept_setConcludedTrue()
    {
        $this->executeAccept();
        $this->assertTrue($this->registrant->concluded);
    }

    public function test_accept_setNoteAccepted()
    {
        $this->executeAccept();
        $this->assertEquals('accepted', $this->registrant->note);
    }

    public function test_accept_alreadyConcluded_throwEx()
    {
        $this->registrant->concluded = true;
        $operation = function () {
            $this->executeAccept();
        };
        $errorDetail = "forbidden: application already concluded";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }

    protected function executeReject()
    {
        $this->registrant->reject();
    }

    public function test_reject_setConcludedFlagTrue()
    {
        $this->executeReject();
        $this->assertTrue($this->registrant->concluded);
    }

    public function test_reject_setNoteStringRejected()
    {
        $this->executeReject();
        $this->assertEquals('rejected', $this->registrant->note);
    }

    public function test_reject_alreadyConcluded_throwEx()
    {
        $this->registrant->concluded = true;
        $operation = function () {
            $this->executeReject();
        };
        $errorDetail = "forbidden: application already concluded";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }

}

class TestableRegistrant extends Registrant
{

    public $program, $id = 'registrant-id', $client, $concluded = false, $note = null;

    public function __construct()
    {
        parent::__construct();
    }

}
