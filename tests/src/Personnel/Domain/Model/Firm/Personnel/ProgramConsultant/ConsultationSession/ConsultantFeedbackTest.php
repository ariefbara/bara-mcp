<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ConsultantFeedbackTest extends TestBase
{
    protected $consultationSession;
    protected $formRecord;
    protected $consultantFeedback;
    protected $id = 'newId', $participantRating = 3;
    protected $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->consultantFeedback = new TestableConsultantFeedback($this->consultationSession, 'id', $this->formRecord, 2);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function executeConstruct()
    {
        return new TestableConsultantFeedback($this->consultationSession, $this->id, $this->formRecord, $this->participantRating);
    }
    public function test_construct_setProperties()
    {
        $consultantFeedback = $this->executeConstruct();
        $this->assertEquals($this->consultationSession, $consultantFeedback->consultationSession);
        $this->assertEquals($this->id, $consultantFeedback->id);
        $this->assertEquals($this->formRecord, $consultantFeedback->formRecord);
        $this->assertEquals($this->participantRating, $consultantFeedback->participantRating);
    }
    public function test_construct_participantRatingLessThanOne_badRequest()
    {
        $this->participantRating = -1;
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: participant rating must be between 1-5";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_participantRatingBiggerThanFive_badRequest()
    {
        $this->participantRating = 6;
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: participant rating must be between 1-5";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_nullParticipantRating_processNormally()
    {
        $this->participantRating = null;
        $this->executeConstruct();
        $this->markAsSuccess();
    }
    
    protected function executeUpdate()
    {
        $this->consultantFeedback->update($this->formRecordData, $this->participantRating);
    }
    public function test_update_updateFormRecordAndRating()
    {
        $this->formRecord->expects($this->once())
                ->method('update')
                ->with($this->formRecordData);
        $this->executeUpdate();
        $this->assertEquals($this->participantRating, $this->consultantFeedback->participantRating);
    }
    public function test_update_participantRatingOutOfBound_badRequest()
    {
        $this->participantRating = 7;
        $operation = function (){
            $this->executeUpdate();
        };
        $errorDetail = "bad request: participant rating must be between 1-5";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
}

class TestableConsultantFeedback extends ConsultantFeedback
{
    public $consultationSession;
    public $id;
    public $formRecord;
    public $participantRating;
}
