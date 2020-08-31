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
    protected $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->participantFeedback = new TestableParticipantFeedback($this->consultationSession, 'id', $this->formRecord);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    public function test_construct_setProperties()
    {
        $participantFeedback = new TestableParticipantFeedback($this->consultationSession, $this->id, $this->formRecord);
        $this->assertEquals($this->consultationSession, $participantFeedback->consultationSession);
        $this->assertEquals($this->id, $participantFeedback->id);
        $this->assertEquals($this->formRecord, $participantFeedback->formRecord);
    }
    public function test_update_udpateFormRecord()
    {
        $this->formRecord->expects($this->once())
                ->method('update')
                ->with($this->formRecordData);
        $this->participantFeedback->update($this->formRecordData);
    }
}

class TestableParticipantFeedback extends ParticipantFeedback
{
    public $consultationSession;
    public $id;
    public $formRecord;
}
