<?php

namespace Client\Domain\Model\Firm\Program;

use Client\Domain\Model\Firm\FeedbackForm;
use DateTimeImmutable;
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSetup = new TestableConsultationSetup();
        $this->consultationSetup->sessionDuration = 45;
        $this->startTime = new DateTimeImmutable();
        
        $this->participantFeedbackForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->consultationSetup->participantFeedbackForm = $this->participantFeedbackForm;
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
        $this->assertEquals($formRecord, $this->consultationSetup->createFormRecordFormParticipantFeedback($id, $formRecordData));
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
