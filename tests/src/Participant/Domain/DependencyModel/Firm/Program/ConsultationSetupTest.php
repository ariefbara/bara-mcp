<?php

namespace Participant\Domain\DependencyModel\Firm\Program;

use DateTimeImmutable;
use Participant\Domain\DependencyModel\Firm\FeedbackForm;
use Participant\Domain\DependencyModel\Firm\IContainParticipantReport;
use Participant\Domain\DependencyModel\Firm\Program;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ConsultationSetupTest extends TestBase
{

    protected $consultationSetup;
    protected $program;
    protected $participantFeedbackForm;
    
    protected $startTime;
    
    protected $mentoring, $formRecordData, $mentorRating = 11;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSetup = new TestableConsultationSetup();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->consultationSetup->program = $this->program;
        $this->consultationSetup->sessionDuration = 45;
        
        $this->participantFeedbackForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->consultationSetup->participantFeedbackForm = $this->participantFeedbackForm;
        
        $this->startTime = new DateTimeImmutable();
        
        $this->mentoring = $this->buildMockOfInterface(IContainParticipantReport::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }

    public function test_programEquals_sameProgram_returnTrue()
    {
        $this->assertTrue($this->consultationSetup->programEquals($this->program));
    }

    public function test_programEquals_differentProgram_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->consultationSetup->programEquals($program));
    }

    protected function executeGetSessionStartEndTimeOf()
    {
        return $this->consultationSetup->getSessionStartEndTimeOf($this->startTime);
    }

    public function test_getSessionStartEndTimeOf_returnDateTimeIntervalOfStartTimePlusDuration()
    {
        $startEndTime = new DateTimeInterval($this->startTime, new DateTimeImmutable('+45 minutes'));
        $this->assertEquals($startEndTime, $this->executeGetSessionStartEndTimeOf());
    }

    public function test_getSessionStartEndTimeOf_differentDuration()
    {
        $this->consultationSetup->sessionDuration = 60;
        $startEndTime = new DateTimeInterval($this->startTime, new DateTimeImmutable('+60 minutes'));
        $this->assertEquals($startEndTime, $this->executeGetSessionStartEndTimeOf());
    }

    public function test_createFormRecordFormParticipantFeedback_returnResultOfParticipantFeedbackFormsCreateFormRecord()
    {
        $this->participantFeedbackForm->expects($this->once())
                ->method('createFormRecord')
                ->with($id = "id", $formRecordData = $this->buildMockOfClass(FormRecordData::class))
                ->willReturn($formRecord = $this->buildMockOfClass(FormRecord::class));
        $this->assertEquals($formRecord,
                $this->consultationSetup->createFormRecordForParticipantFeedback($id, $formRecordData));
    }
    
    protected function assertUsableInProgram()
    {
        $this->consultationSetup->assertUsableInProgram($this->program);
    }
    public function test_assertUsableInProgram_usableSetup_void()
    {
        $this->assertUsableInProgram();
        $this->markAsSuccess();
    }
    public function test_assertUsableInProgram_inactiveSetup_forbidden()
    {
        $this->consultationSetup->removed = true;
        $this->assertRegularExceptionThrowed(function() {
            $this->assertUsableInProgram();
        }, 'Forbidden', 'forbidden: unuseable consultation setup, either inactive or belongs to other program');
    }
    public function test_assertUsableInProgram_differentProgram_forbidden()
    {
        $this->consultationSetup->program = $this->buildMockOfClass(Program::class);
        $this->assertRegularExceptionThrowed(function() {
            $this->assertUsableInProgram();
        }, 'Forbidden', 'forbidden: unuseable consultation setup, either inactive or belongs to other program');
    }
    
    protected function processReportIn()
    {
        $this->consultationSetup->processReportIn($this->mentoring, $this->formRecordData, $this->mentorRating);
    }
    public function test_processReportIn_forwardRequestToParticipantFeedbackForm()
    {
        $this->participantFeedbackForm->expects($this->once())
                ->method('processReportIn')
                ->with($this->mentoring, $this->formRecordData, $this->mentorRating);
        $this->processReportIn();
    }

}

class TestableConsultationSetup extends ConsultationSetup
{

    public $program;
    public $id;
    public $sessionDuration;
    public $participantFeedbackForm;
    public $removed = false;

    function __construct()
    {
        parent::__construct();
    }

}
