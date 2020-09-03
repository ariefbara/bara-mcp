<?php

namespace Notification\Domain\Model;

use Query\Domain\Model\FirmWhitelableInfo;
use Tests\TestBase;

class FirmTest extends TestBase
{
    protected $firm;
    protected $firmWhitelableInfo;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = new TestableFirm();
        $this->firmWhitelableInfo = $this->buildMockOfClass(FirmWhitelableInfo::class);
        $this->firm->firmWhitelableInfo = $this->firmWhitelableInfo;
    }
    
    public function test_getFirwmgetWhitelableUrl_returnWhitelableUrl()
    {
        $this->firmWhitelableInfo->expects($this->once())
                ->method('getUrl');
        $this->firm->getWhitelableUrl();
    }
    public function test_getMailSender_returnFirmMailSender()
    {
        $this->firmWhitelableInfo->expects($this->once())->method('getMailSenderAddress')->willReturn('firm@email.org');
        $this->firmWhitelableInfo->expects($this->once())->method('getMailSenderName')->willReturn('firm');
        $firmMailSender = new Firm\FirmMailSender('firm@email.org', 'firm');
        $this->assertEquals($firmMailSender, $this->firm->getMailSender());
        
    }
}

class TestableFirm extends Firm
{
    public $id;
    public $name;
    public $firmWhitelableInfo;
    public $suspended = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
