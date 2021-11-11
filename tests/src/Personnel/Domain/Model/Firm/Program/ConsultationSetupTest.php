<?php

namespace Personnel\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Personnel\Domain\Model\Firm\ContainMentorReport;
use Personnel\Domain\Model\Firm\FeedbackForm;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ConsultationSetupTest extends TestBase
{

    protected $consultationSetup, $programId = 'programId';
    protected $startTime;
    protected $participantFeedbackForm;
    protected $consultantFeedbackForm;

    protected $containMentorReport, $formRecordData, $participantRating = 7;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSetup = new TestableConsultationSetup();
        $this->consultationSetup->programId = $this->programId;
        $this->consultationSetup->sessionDuration = 45;
        $this->startTime = new DateTimeImmutable();
        
        $this->participantFeedbackForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->consultationSetup->participantFeedbackForm = $this->participantFeedbackForm;
        $this->consultantFeedbackForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->consultationSetup->consultantFeedbackForm = $this->consultantFeedbackForm;
        
        $this->containMentorReport = $this->buildMockOfInterface(ContainMentorReport::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
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
    
    public function test_createFormRecordForConsultantFeedback_returnResultOfConsultantFeedbackFormsCreateFormRecordMethod()
    {
        $this->consultantFeedbackForm->expects($this->once())
                ->method('createFormRecord')
                ->with($id = 'id', $formRecordData = $this->buildMockOfClass(FormRecordData::class))
                ->willReturn($formRecord = $this->buildMockOfClass(FormRecord::class));
        $this->assertEquals($formRecord, $this->consultationSetup->createFormRecordForConsultantFeedback($id, $formRecordData));
    }
    
    protected function usableInProgram()
    {
        return $this->consultationSetup->usableInProgram($this->programId);
    }
    public function test_usableInProgram_sameProgram_returnTrue()
    {
        $this->assertTrue($this->usableInProgram());
    }
    public function test_usableInProgram_differentProgram_returnFalse()
    {
        $this->consultationSetup->programId = 'differentProgramId';
        $this->assertFalse($this->usableInProgram());
    }
    public function test_usableInProgram_inactiveConsultationSetup_returnFalse()
    {
        $this->consultationSetup->removed = true;
        $this->assertFalse($this->usableInProgram());
    }
    
    protected function processReportIn()
    {
        $this->consultationSetup->processReportIn($this->containMentorReport, $this->formRecordData, $this->participantRating);
    }
    public function test_processReportIn_forwardRequestToConsultantFeedbackForm()
    {
        $this->consultantFeedbackForm->expects($this->once())
                ->method('processReportIn')
                ->with($this->containMentorReport, $this->formRecordData, $this->participantRating);
        $this->processReportIn();
    }
    
}

class TestableConsultationSetup extends ConsultationSetup
{
    public $programId;
    public $id;
    public $name;
    public $sessionDuration;
    public $participantFeedbackForm;
    public $consultantFeedbackForm;
    public $removed = false;

    function __construct()
    {
        parent::__construct();
    }

}
