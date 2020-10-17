<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Config\EventList;
use Personnel\Domain\Model\Firm\ {
    Personnel\ProgramConsultant,
    Personnel\ProgramConsultant\ConsultationSession\ConsultantFeedback,
    Personnel\ProgramConsultant\ConsultationSession\ConsultationSessionActivityLog,
    Program\ConsultationSetup,
    Program\Participant
};
use Resources\Domain\ {
    Event\CommonEvent,
    ValueObject\DateTimeInterval
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ConsultationSessionTest extends TestBase
{
    protected $programConsultant;
    protected $participant;
    protected $consultationSetup;
    protected $startEndTime;
    protected $consultationSession;
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
        $this->consultationSession->consultationSessionActivityLogs->clear();
        
        $this->consultantFeedback = $this->buildMockOfClass(ConsultantFeedback::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function executeConstruct()
    {
        return new TestableConsultationSession(
                $this->programConsultant, $this->id, $this->participant, $this->consultationSetup, $this->startEndTime);
    }
    public function test_construct_setProperties()
    {
        $consultationSession = $this->executeConstruct();
        $this->assertEquals($this->programConsultant, $consultationSession->programConsultant);
        $this->assertEquals($this->id, $consultationSession->id);
        $this->assertEquals($this->participant, $consultationSession->participant);
        $this->assertEquals($this->consultationSetup, $consultationSession->consultationSetup);
        $this->assertEquals($this->startEndTime, $consultationSession->startEndTime);
    }
    public function test_construct_addConsultationSessionActivityLog()
    {
        $consultationSession = $this->executeConstruct();
        $this->assertEquals(1, $consultationSession->consultationSessionActivityLogs->count());
        $this->assertInstanceOf(ConsultationSessionActivityLog::class, $consultationSession->consultationSessionActivityLogs->first());
    }
    public function test_construct_recordConsultationRequestAcceptedEvent()
    {
        $consultationSession = $this->executeConstruct();
        $event = new CommonEvent(EventList::CONSULTATION_SESSION_SCHEDULED_BY_CONSULTANT, $this->id);
        $this->assertEquals($event, $consultationSession->recordedEvents[0]);
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
    public function test_setConsultantFeedback_addActivityLog()
    {
        $this->executeSetConsultantFeedback();
        $this->assertEquals(1, $this->consultationSession->consultationSessionActivityLogs->count());
        $this->assertInstanceOf(ConsultationSessionActivityLog::class, $this->consultationSession->consultationSessionActivityLogs->first());
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
    public $consultationSessionActivityLogs;
    
    public $recordedEvents;

}
