<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\Model\Firm\Program;
use Tests\TestBase;

class ConsultationSetupTest extends TestBase
{

    protected $consultationSetup;
    protected $program;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->consultationSetup = new TestableConsultationSetup();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->consultationSetup->program = $this->program;
    }
    
    public function test_getFirmWhitelableUrl_returnProgramsGetFirmWhitelableUrl()
    {
        $this->program->expects($this->once())
                ->method('getFirmWhitelableUrl');
        $this->consultationSetup->getFirmWhitelableUrl();
    }
    
    public function test_getFirmMailSender_returnProgramsGetFirmMailSender()
    {
        $this->program->expects($this->once())
                ->method('getFirmMailSender');
        $this->consultationSetup->getFirmMailSender();
    }

}

class TestableConsultationSetup extends ConsultationSetup
{
    public $program;
    public $id;
    public $name;
    public $removed = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
