<?php

namespace Notification\Domain\Model;

use Notification\Domain\Model\Firm\FirmFileInfo;
use Query\Domain\Model\FirmWhitelableInfo;
use Tests\TestBase;

class FirmTest extends TestBase
{
    protected $firm;
    protected $firmWhitelableInfo;
    protected $logo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = new TestableFirm();
        $this->firmWhitelableInfo = $this->buildMockOfClass(FirmWhitelableInfo::class);
        $this->firm->firmWhitelableInfo = $this->firmWhitelableInfo;
        
        $this->logo = $this->buildMockOfClass(FirmFileInfo::class);
        $this->firm->logo = $this->logo;
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
    
    public function test_getLogoPath_returnLogoGetFullyQualifiedNameResult()
    {
        $this->logo->expects($this->once())
                ->method("getFullyQualifiedFileName");
        $this->firm->getLogoPath();
    }
    public function test_getLogoPath_emptyLogo_returnNull()
    {
        $this->firm->logo = null;
        $this->assertNull($this->firm->getLogoPath());
    }
    
}

class TestableFirm extends Firm
{
    public $id;
    public $firmWhitelableInfo;
    public $logo;
    
    function __construct()
    {
        parent::__construct();
    }
}
