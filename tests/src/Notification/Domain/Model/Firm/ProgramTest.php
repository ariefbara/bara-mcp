<?php

namespace Notification\Domain\Model\Firm;

use Notification\Domain\Model\Firm;
use Tests\TestBase;

class ProgramTest extends TestBase
{
    protected $program;
    protected $firm;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->program = new TestableProgram();
        
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->program->firm = $this->firm;
    }
    
    public function test_getFirmDomain_returnFirmGetDomainResult()
    {
        $this->firm->expects($this->once())
                ->method("getDomain");
        $this->program->getFirmDomain();
    }
    public function test_getFirmMailSenderAddress_returnFirmsGetMailSenderAddressResult()
    {
        $this->firm->expects($this->once())
                ->method("getMailSenderAddress");
        $this->program->getFirmMailSenderAddress();
    }
    public function test_getFirmMailSenderName_returnFirmsGetMailSenderNameResult()
    {
        $this->firm->expects($this->once())
                ->method("getMailSenderName");
        $this->program->getFirmMailSenderName();
    }
    
    public function test_getFirmLogoPath_returnFirmGetLogoPathResult()
    {
        $this->firm->expects($this->once())
                ->method("getLogoPath");
        $this->program->getFirmLogoPath();
    }
}

class TestableProgram extends Program
{
    public $firm;
    public $id;
    
    function __construct()
    {
        parent::__construct();
    }
}
