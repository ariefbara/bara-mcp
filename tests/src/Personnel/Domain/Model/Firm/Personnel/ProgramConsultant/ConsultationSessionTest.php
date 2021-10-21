<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Config\EventList;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession\ConsultantFeedback;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession\ConsultationSessionActivityLog;
use Personnel\Domain\Model\Firm\Program\ConsultationSetup;
use Personnel\Domain\Model\Firm\Program\Participant;
use Resources\Domain\Event\CommonEvent;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use SharedContext\Domain\ValueObject\ConsultationChannel;
use SharedContext\Domain\ValueObject\ConsultationSessionType;
use Tests\TestBase;

class ConsultationSessionTest extends TestBase
{
    protected $programConsultant;
    protected $participant;
    protected $consultationSetup;
    protected $startEndTime;
    protected $channel;
    protected $sessionType;
    protected $consultationSession;
    protected $id = 'id';
    protected $consultantFeedback, $participantRating = 4;
    protected $formRecordData;
    protected $consultationRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->programConsultant = $this->buildMockOfClass(ProgramConsultant::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->startEndTime = $this->buildMockOfClass(DateTimeInterval::class);
        $this->channel = $this->buildMockOfClass(ConsultationChannel::class);
        $this->sessionType = $this->buildMockOfClass(ConsultationSessionType::class);
        $this->consultationSession = new TestableConsultationSession(
                $this->programConsultant, 'id', $this->participant, $this->consultationSetup, $this->startEndTime, 
                $this->channel, $this->sessionType);
        $this->consultationSession->consultationSessionActivityLogs->clear();
        
        $this->consultantFeedback = $this->buildMockOfClass(ConsultantFeedback::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
    }
    
    protected function executeConstruct()
    {
        return new TestableConsultationSession(
                $this->programConsultant, $this->id, $this->participant, $this->consultationSetup, $this->startEndTime, 
                $this->channel, $this->sessionType);
    }
    public function test_construct_setProperties()
    {
        $consultationSession = $this->executeConstruct();
        $this->assertEquals($this->programConsultant, $consultationSession->programConsultant);
        $this->assertEquals($this->id, $consultationSession->id);
        $this->assertEquals($this->participant, $consultationSession->participant);
        $this->assertEquals($this->consultationSetup, $consultationSession->consultationSetup);
        $this->assertEquals($this->startEndTime, $consultationSession->startEndTime);
        $this->assertEquals($this->channel, $consultationSession->channel);
        $this->assertEquals($this->sessionType, $consultationSession->sessionType);
        $this->assertFalse($consultationSession->cancelled);
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
    
    protected function executeIntersectWithConsultationRequest()
    {
        $this->consultationRequest->expects($this->any())
                ->method('scheduleIntersectWith')
                ->willReturn(true);
        return $this->consultationSession->intersectWithConsultationRequest($this->consultationRequest);
    }
    public function test_intersectWithConsultationRequest_returnResultOfStartEndTimeIntersectComparison()
    {
        $this->consultationRequest->expects($this->once())
                ->method('scheduleIntersectWith')
                ->with($this->consultationSession->startEndTime)
                ->willReturn(true);
        $this->assertTrue($this->executeIntersectWithConsultationRequest());
    }
    public function test_intersectWithConsultationRequest_cancelledSession_returnFalse()
    {
        $this->consultationSession->cancelled = true;
        $this->assertFalse($this->executeIntersectWithConsultationRequest());
    }
    
    protected function executeSetConsultantFeedback()
    {
        $this->consultationSession->setConsultantFeedback($this->formRecordData, $this->participantRating);
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
                ->with($this->formRecordData, $this->participantRating);
        $this->executeSetConsultantFeedback();
    }
    public function test_setConsultantFeedback_addActivityLog()
    {
        $this->executeSetConsultantFeedback();
        $this->assertEquals(1, $this->consultationSession->consultationSessionActivityLogs->count());
        $this->assertInstanceOf(ConsultationSessionActivityLog::class, $this->consultationSession->consultationSessionActivityLogs->first());
    }
    public function test_setConsultantFeedback_cancelledSession_forbidden()
    {
        $this->consultationSession->cancelled = true;
        $operation = function (){
            $this->executeSetConsultantFeedback();
        };
        $errorDetail = "forbidden: unable to submit report on cancelled session";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function cancel()
    {
        $this->sessionType->expects($this->any())
                ->method('canBeCancelled')
                ->willReturn(true);
        $this->consultationSession->cancel();
    }
    public function test_cancel_setCancelled()
    {
        $this->cancel();
        $this->assertTrue($this->consultationSession->cancelled);
    }
    public function test_cancel_uncancelledType_forbidden()
    {
        $this->sessionType->expects($this->once())
                ->method('canBeCancelled')
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function() {
            $this->cancel();
        }, 'Forbidden', 'forbidden: unable to cancel handsaking session');
    }
    
    protected function deny()
    {
        $this->consultationSession->deny();
    }
    public function test_deny_denySessionType()
    {
        $this->sessionType->expects($this->once())
                ->method('deny')
                ->willReturn($deniedType = $this->buildMockOfClass(ConsultationSessionType::class));
        
        $this->deny();
        $this->assertSame($deniedType, $this->consultationSession->sessionType);
    }
    public function test_deny_cancelConsultationSession()
    {
        $this->deny();
        $this->assertTrue($this->consultationSession->cancelled);
    }
    
    protected function approve()
    {
        $this->consultationSession->approve();
    }
    public function test_approve_approveSessionType()
    {
        $this->sessionType->expects($this->once())
                ->method('approve')
                ->willReturn($approvedType = $this->buildMockOfClass(ConsultationSessionType::class));
        $this->approve();
        $this->assertSame($approvedType, $this->consultationSession->sessionType);
    }
    
    protected function assertManageableByMentor()
    {
        $this->consultationSession->assertManageableByMentor($this->programConsultant);
    }
    public function test_assertManageableByMentor_sameMentor_void()
    {
        $this->assertManageableByMentor();
        $this->markAsSuccess();
    }
    public function test_assertManageableByMentor_differentMentor_forbidden()
    {
        $this->consultationSession->programConsultant = $this->buildMockOfClass(ProgramConsultant::class);
        $this->assertRegularExceptionThrowed(function() {
            $this->assertManageableByMentor();
        }, 'Forbidden', 'forbidden: consultation session is unmanageable, either already cancelled or doesn\'t belongs to mentor');
    }
    public function test_assertManageableByMentor_cancelled_forbidden()
    {
        $this->consultationSession->cancelled = true;
        $this->assertRegularExceptionThrowed(function() {
            $this->assertManageableByMentor();
        }, 'Forbidden', 'forbidden: consultation session is unmanageable, either already cancelled or doesn\'t belongs to mentor');
    }

}

class TestableConsultationSession extends ConsultationSession
{

    public $programConsultant;
    public $id;
    public $participant;
    public $consultationSetup;
    public $startEndTime;
    public $channel;
    public $sessionType;
    public $cancelled;
    public $consultantFeedback;
    public $consultationSessionActivityLogs;
    
    public $recordedEvents;

}
