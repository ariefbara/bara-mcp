<?php

namespace Personnel\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Personnel\Domain\Model\Firm\ConsultationFeedbackForm;
use Resources\Domain\ValueObject\DateTimeInterval;
use Shared\Domain\Model\ {
    FormRecord,
    FormRecordData
};
use Tests\TestBase;

class ConsultationSetupTest extends TestBase
{

    protected $consultationSetup;
    protected $startTime;
    protected $participantFeedbackForm;
    protected $consultantFeedbackForm;



    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSetup = new TestableConsultationSetup();
        $this->consultationSetup->sessionDuration = 45;
        $this->startTime = new DateTimeImmutable();
        
        $this->participantFeedbackForm = $this->buildMockOfClass(ConsultationFeedbackForm::class);
        $this->consultationSetup->participantFeedbackForm = $this->participantFeedbackForm;
        $this->consultantFeedbackForm = $this->buildMockOfClass(ConsultationFeedbackForm::class);
        $this->consultationSetup->consultantFeedbackForm = $this->consultantFeedbackForm;
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
    
}

class TestableConsultationSetup extends ConsultationSetup
{
    public $program;
    public $id;
    public $name;
    public $sessionDuration;
    public $participantFeedbackForm;
    public $consultantFeedbackForm;

    function __construct()
    {
        parent::__construct();
    }

}
