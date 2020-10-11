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
    
    public function test_getDomain_returnWhitelableInfosGetUrlResult()
    {
        $this->firmWhitelableInfo->expects($this->once())
                ->method("getUrl");
        $this->firm->getDomain();
    }
    public function test_getMailSenderAddress_returnWhitelableInfosGetMailSenderAddressResult()
    {
        $this->firmWhitelableInfo->expects($this->once())
                ->method("getMailSenderAddress");
        $this->firm->getMailSenderAddress();
    }
    public function test_getMailSenderName_returnWhitelableInfosGetMailSenderNameResult()
    {
        $this->firmWhitelableInfo->expects($this->once())
                ->method("getMailSenderName");
        $this->firm->getMailSenderName();
    }
    
}

class TestableFirm extends Firm
{
    public $id;
    public $firmWhitelableInfo;
    
    function __construct()
    {
        parent::__construct();
    }
}
