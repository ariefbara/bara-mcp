<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\Model\Firm\ {
    Personnel\ProgramConsultant,
    Personnel\ProgramConsultant\ConsultationSession\ConsultantFeedback,
    Program\ConsultationSetup,
    Program\Participant
};
use Resources\Domain\ValueObject\DateTimeInterval;
use Shared\Domain\Model\FormRecordData;
use Tests\TestBase;

class ConsultationSessionTest extends TestBase
{
    protected $consultationSession;
    protected $programConsultant;
    protected $participant;
    protected $consultationSetup;
    protected $startEndTime;
    protected $id = 'id';
    protected $consultantFeedback;
    protected $formRecordData;


    protected function setUp(): void
    {
        parent::setUp();
        $this->programConsultant = $this->buildMockOfClass(ProgramConsultant::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->startEndTime = $this->buildMockOfClass(DateTimeInterval::class);
        $this->consultationSession = new TestableConsultationSession($this->programConsultant, 'id', $this->participant, $this->consultationSetup, $this->startEndTime);
        
        $this->consultantFeedback = $this->buildMockOfClass(ConsultantFeedback::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    public function test_construct_setProperties()
    {
        $consultationSession = new TestableConsultationSession($this->programConsultant, $this->id, $this->participant, $this->consultationSetup, $this->startEndTime);
        $this->assertEquals($this->programConsultant, $consultationSession->programConsultant);
        $this->assertEquals($this->id, $consultationSession->id);
        $this->assertEquals($this->participant, $consultationSession->participant);
        $this->assertEquals($this->consultationSetup, $consultationSession->consultationSetup);
        $this->assertEquals($this->startEndTime, $consultationSession->startEndTime);
    }
    public function test_intersectWithConsultationRequest_returnResultOfStartEndTimeIntersectComparison()
    {
        $this->startEndTime->expects($this->once())
                ->method('intersectWith')
                ->with($startEndTime = $this->buildMockOfClass(DateTimeInterval::class))
                ->willReturn(true);
        $consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $consultationRequest->expects($this->once())
                ->method('getStartEndTime')
                ->willReturn($startEndTime);
        $this->assertTrue($this->consultationSession->intersectWithConsultationRequest($consultationRequest));
    }
    
    protected function executeSetConsultantFeedback()
    {
        $this->consultationSession->setConsultantFeedback($this->formRecordData);
    }

    public function test_setConsultantFeedback_setConsultantFeedback()
    {
        $this->executeSetConsultantFeedback();
        $this->assertInstanceOf(ConsultantFeedback::class, $this->consultationSession->consultantFeedback);
    }

    public function test_setConsultantFeedback_alreadyHasConsultantFeedback_updateExistingConsultantFeedback()
    {
        $this->consultationSession->consultantFeedback = $this->consultantFeedback;
        $this->consultantFeedback->expects($this->once())
                ->method('update')
                ->with($this->formRecordData);
        $this->executeSetConsultantFeedback();
    }

}

class TestableConsultationSession extends ConsultationSession
{

    public $programConsultant;
    public $id;
    public $participant;
    public $consultationSetup;
    public $startEndTime;
    public $consultantFeedback;

}
