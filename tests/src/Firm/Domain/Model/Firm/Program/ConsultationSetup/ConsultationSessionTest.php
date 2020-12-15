<?php

namespace Firm\Domain\Model\Firm\Program\ConsultationSetup;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\ConsultationSetup;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\ValueObject\ConsultationChannel;
use Tests\TestBase;

class ConsultationSessionTest extends TestBase
{
    protected $consultationSession;
    protected $consultationSetup;
    protected $startEndTime;
    protected $media = "media", $address = "address";
    protected $program;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = new TestableConsultationSession();
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultationSession->consultationSetup = $this->consultationSetup;
        
        $this->startEndTime = $this->buildMockOfClass(DateTimeInterval::class);
        $this->consultationSession->startEndTime = $this->startEndTime;
        
        $this->program = $this->buildMockOfClass(Program::class);
    }
    
    protected function executeDisableUpcomingSession()
    {
        $this->startEndTime->expects($this->any())
                ->method("isUpcoming")
                ->willReturn(true);
        $this->consultationSession->disableUpcomingSession();
    }
    public function test_disableUpcomingSession_cancelSessionAndSetNote()
    {
        $this->executeDisableUpcomingSession();
        $this->assertTrue($this->consultationSession->cancelled);
        $this->assertEquals("disabled by system", $this->consultationSession->note);
    }
    public function test_disableUpcomingSession_alreadyCancelled_nop()
    {
        $this->consultationSession->cancelled = true;
        $this->executeDisableUpcomingSession();
        $this->assertNull($this->consultationSession->note);
    }
    public function test_disableUpcomingSession_alreadyPassed_nop()
    {
        $this->startEndTime->expects($this->once())
                ->method("isUpcoming")
                ->willReturn(false);
        $this->executeDisableUpcomingSession();
        $this->assertFalse($this->consultationSession->cancelled);
        $this->assertNull($this->consultationSession->note);
    }
    
    public function test_changeChannel_updateChannelProperties()
    {
        $this->consultationSession->changeChannel($this->media, $this->address);
        $channel = new ConsultationChannel($this->media, $this->address);
        $this->assertEquals($channel, $this->consultationSession->channel);
    }
    
    public function test_belongsToProgram_returnConsultationSetupsBelongsToProgramResult()
    {
        $this->consultationSetup->expects($this->once())
                ->method("belongsToProgram")
                ->with($this->program);
        $this->consultationSession->belongsToProgram($this->program);
    }
}

class TestableConsultationSession extends ConsultationSession
{
    public $consultationSetup;
    public $id;
    public $consultant;
    public $startEndTime;
    public $channel;
    public $cancelled = false;
    public $note;
    
    function __construct()
    {
        parent::__construct();
    }
}
