<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\Model\Firm\Program;
use Tests\TestBase;

class MeetingTypeTest extends TestBase
{
    protected $meetingType;
    protected $program;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->meetingType = new TestableMeetingType();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->meetingType->program = $this->program;
    }
    
    public function test_getFirmDomain_returnProgramsGetFirmDomainResult()
    {
        $this->program->expects($this->once())->method("getFirmDomain");
        $this->meetingType->getFirmDomain();
    }
    
    public function test_getFirmLogoPath_returnProgramsGetFirmLogoPathResult()
    {
        $this->program->expects($this->once())->method("getFirmLogoPath");
        $this->meetingType->getFirmLogoPath();
    }
    
    public function test_getFirmMailSenderAddress_returnProgramsGetFirmMailSenderAddressResult()
    {
        $this->program->expects($this->once())->method("getFirmMailSenderAddress");
        $this->meetingType->getFirmMailSenderAddress();
    }
    
    public function test_getFirmMailSenderName_returnProgramsGetFirmMailSenderNameResult()
    {
        $this->program->expects($this->once())->method("getFirmMailSenderName");
        $this->meetingType->getFirmMailSenderName();
    }
}

class TestableMeetingType extends MeetingType
{
    public $program;
    public $id;
    
    function __construct()
    {
        parent::__construct();
    }
}
