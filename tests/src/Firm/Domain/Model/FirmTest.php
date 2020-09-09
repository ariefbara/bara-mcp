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
