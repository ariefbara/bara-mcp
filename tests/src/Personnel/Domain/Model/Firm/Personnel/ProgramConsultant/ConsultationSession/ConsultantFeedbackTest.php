<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;
use SharedContext\Domain\Model\SharedEntity\ {
    FormRecord,
    FormRecordData
};
use Tests\TestBase;

class ConsultantFeedbackTest extends TestBase
{
    protected $consultationSession;
    protected $formRecord;
    protected $consultantFeedback;
    protected $id = 'newId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->consultantFeedback = new TestableConsultantFeedback($this->consultationSession, 'id', $this->formRecord);
    }
    public function test_construct_setProperties()
    {
        $consultantFeedback = new TestableConsultantFeedback($this->consultationSession, $this->id, $this->formRecord);
        $this->assertEquals($this->consultationSession, $consultantFeedback->consultationSession);
        $this->assertEquals($this->id, $consultantFeedback->id);
        $this->assertEquals($this->formRecord, $consultantFeedback->formRecord);
    }
    
    public function test_update_updateFormRecord()
    {
        $formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $this->formRecord->expects($this->once())
                ->method('update')
                ->with($formRecordData);
        $this->consultantFeedback->update($formRecordData);
    }
}

class TestableConsultantFeedback extends ConsultantFeedback
{
    public $consultationSession;
    public $id;
    public $formRecord;
}
