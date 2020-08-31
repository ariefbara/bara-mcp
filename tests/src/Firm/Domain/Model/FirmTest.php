<?php

namespace Firm\Domain\Model;

use Firm\Domain\ {
    Event\ClientSignupAcceptedEvent,
    Model\Firm\Client,
    Model\Firm\ClientData
};
use Tests\TestBase;

class FirmTest extends TestBase
{
    protected $firm;
    
    protected $clientId = 'clientId', $clientData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = new TestableFirm();
        $this->firm->suspended = false;
        
        $this->clientData = new ClientData('hadi', 'pranoto', 'client@email.org', 'password123');
    }
    
    protected function executeAcceptClientSignup()
    {
        return $this->firm->acceptClientSignup($this->clientId, $this->clientData);
    }
    
    public function test_acceptClientSignup_returnClient()
    {
        $this->assertInstanceOf(Client::class, $this->executeAcceptClientSignup());
    }
    public function test_acceptClientSignup_firmSuspended_forbidden()
    {
        $this->firm->suspended = true;
        $operation = function (){
            $this->executeAcceptClientSignup();
        };
        $errorDetail = 'forbidden: unable to signup to suspended firm';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_acceptClientSignup_addClientSignupAcceptedEventToRecordedEvent()
    {
        $event = new ClientSignupAcceptedEvent($this->firm->id, $this->clientId);
        $this->executeAcceptClientSignup();
        $this->assertEquals($event, $this->firm->recordedEvents[0]);
    }
}

class TestableFirm extends Firm
{
    public $id = 'firmId';
    public $name;
    public $identifier;
    public $whitelableInfo;
    public $suspended = false;
    public $recordedEvents;
    
    function __construct()
    {
        parent::__construct();
    }
}
