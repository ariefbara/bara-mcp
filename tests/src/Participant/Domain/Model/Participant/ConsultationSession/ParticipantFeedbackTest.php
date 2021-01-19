<?php

namespace Participant\Domain\Model\Participant\ConsultationSession;

use Participant\Domain\Model\Participant\ConsultationSession;
use SharedContext\Domain\Model\SharedEntity\ {
    FormRecord,
    FormRecordData
};
use Tests\TestBase;

class ParticipantFeedbackTest extends TestBase
{
    protected $participantFeedback;
    protected $consultationSession;
    protected $formRecord;
    protected $id = 'newId';
    protected $formRecordData, $mentorRating = 4;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->participantFeedback = new TestableParticipantFeedback($this->consultationSession, 'id', $this->formRecord, 2);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function executeConstruct()
    {
        return new TestableParticipantFeedback($this->consultationSession, $this->id, $this->formRecord, $this->mentorRating);
    }
    public function test_construct_setProperties()
    {
        $participantFeedback = $this->executeConstruct();
        $this->assertEquals($this->consultationSession, $participantFeedback->consultationSession);
        $this->assertEquals($this->id, $participantFeedback->id);
        $this->assertEquals($this->formRecord, $participantFeedback->formRecord);
        $this->assertEquals($this->mentorRating, $participantFeedback->mentorRating);
    }
    public function test_construct_ratingBiggerThanFive_badRequest()
    {
        $this->mentorRating = 6;
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: mentor rating must be betwenn 1-5";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_ratingLessThanOne_badRequest()
    {
        $this->mentorRating = 0;
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: mentor rating must be betwenn 1-5";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_nullRating_processNormally()
    {
        $this->mentorRating = null;
        $this->executeConstruct();
        $this->markAsSuccess();
    }
    
    protected function executeUpdate()
    {
        $this->participantFeedback->update($this->formRecordData, $this->mentorRating);
    }
    public function test_update_udpateFormRecordAndMentorRating()
    {
        $this->formRecord->expects($this->once())
                ->method('update')
                ->with($this->formRecordData);
        $this->executeUpdate();
        $this->assertEquals($this->mentorRating, $this->participantFeedback->mentorRating);
    }
    public function test_udpate_ratingOutOfBounds_badRequest()
    {
        $this->mentorRating = 6;
        $operation = function (){
            $this->executeUpdate();
        };
        $errorDetail = "bad request: mentor rating must be betwenn 1-5";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
}

class TestableParticipantFeedback extends ParticipantFeedback
{
    public $consultationSession;
    public $id;
    public $formRecord;
    public $mentorRating;
}
