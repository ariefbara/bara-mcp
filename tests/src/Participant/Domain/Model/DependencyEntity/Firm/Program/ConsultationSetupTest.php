<?php

namespace Participant\Domain\Model\DependencyEntity\Firm\Program;

use DateTimeImmutable;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\Model\ {
    Firm\FeedbackForm,
    SharedEntity\FormRecord,
    SharedEntity\FormRecordData
};
use Tests\TestBase;

class ConsultationSetupTest extends TestBase
{

    protected $consultationSetup, $programId = 'programId';
    protected $participantFeedbackForm;
    
    protected $startTime;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSetup = new TestableConsultationSetup();
        $this->consultationSetup->programId = $this->programId;
        $this->consultationSetup->sessionDuration = 45;
        
        $this->participantFeedbackForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->consultationSetup->participantFeedbackForm = $this->participantFeedbackForm;
        
        $this->startTime = new DateTimeImmutable();
    }

    protected function executeProgramIdEquals()
    {
        return $this->consultationSetup->programIdEquals($this->programId);
    }

    public function test_programEquals_sameProgram_returnTrue()
    {
        $this->assertTrue($this->executeProgramIdEquals());
    }

    public function test_programEquals_differentProgram_returnFalse()
    {
        $this->consultationSetup->programId = 'different';
        $this->assertFalse($this->executeProgramIdEquals());
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

    public $programId;
    public $id;
    public $sessionDuration;
    public $participantFeedbackForm;
    public $removed = false;

    function __construct()
    {
        parent::__construct();
    }

}
