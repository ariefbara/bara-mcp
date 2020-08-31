<?php

namespace SharedContext\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\Model\ {
    Firm\FeedbackForm,
    Firm\Program,
    SharedEntity\FormRecord,
    SharedEntity\FormRecordData
};
use Tests\TestBase;

class ConsultationSetupTest extends TestBase
{

    protected $consultationSetup;
    protected $program;
    protected $startTime;
    protected $participantFeedbackForm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSetup = new TestableConsultationSetup();
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->consultationSetup->program = $this->program;
        
        $this->consultationSetup->sessionDuration = 45;
        
        $this->startTime = new DateTimeImmutable();

        $this->participantFeedbackForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->consultationSetup->participantFeedbackForm = $this->participantFeedbackForm;
    }

    protected function executeProgramEquals()
    {
        return $this->consultationSetup->programEquals($this->program);
    }

    public function test_programEquals_sameProgram_returnTrue()
    {
        $this->assertTrue($this->executeProgramEquals());
    }

    public function test_programEquals_differentProgram_returnFalse()
    {
        $this->consultationSetup->program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->executeProgramEquals());
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
