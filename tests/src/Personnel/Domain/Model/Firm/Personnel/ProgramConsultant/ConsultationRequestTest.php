<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Config\EventList;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest\ConsultationRequestActivityLog;
use Personnel\Domain\Model\Firm\Program\ConsultationSetup;
use Personnel\Domain\Model\Firm\Program\Participant;
use Resources\Domain\Event\CommonEvent;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\Model\SharedEntity\ConsultationRequestStatusVO;
use SharedContext\Domain\ValueObject\ConsultationChannel;
use Tests\TestBase;

class ConsultationRequestTest extends TestBase
{

    protected $consultationSetup, $participant, $programConsultant;
    protected $startEndTime;
    protected $consultationRequest;
    protected $startTime;
    protected $media = "new media";
    protected $address = "new address";
    protected $otherConsultationRequest;
    protected $otherSchedule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->startEndTime = $this->buildMockOfClass(DateTimeInterval::class);
        $this->channel = $this->buildMockOfClass(ConsultationChannel::class);
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->programConsultant = $this->buildMockOfClass(ProgramConsultant::class);

        $this->consultationRequest = new TestableConsultationRequest();
        $this->consultationRequest->consultationSetup = $this->consultationSetup;
        $this->consultationRequest->id = 'negotiate-schedule-id';
        $this->consultationRequest->participant = $this->participant;
        $this->consultationRequest->programConsultant = $this->programConsultant;
        $this->consultationRequest->startEndTime = $this->startEndTime;
        $this->consultationRequest->channel = $this->channel;
        $this->consultationRequest->status = new ConsultationRequestStatusVO('proposed');
        $this->consultationRequest->consultationRequestActivityLogs = new ArrayCollection();

        $this->startTime = new DateTimeImmutable('+1 days');
        $this->otherConsultationRequest = new TestableConsultationRequest();
        $this->otherConsultationRequest->startEndTime = $this->startEndTime;
        $this->otherSchedule = $this->buildMockOfClass(DateTimeInterval::class);
    }
    
    protected function getConsultationRequestData()
    {
        return new ConsultationRequestData($this->startTime, $this->media, $this->address);
    }
    
    protected function executeScheduleIntersectWith()
    {
        $this->startEndTime->expects($this->any())
                ->method("intersectWith")
                ->willReturn(true);
        return $this->consultationRequest->scheduleIntersectWith($this->otherSchedule);
    }
    public function test_scheduleIntersectWith_returnSchedulesIntersectWithResult()
    {
        $this->startEndTime->expects($this->once())
                ->method("intersectWith")
                ->with($this->otherSchedule);
        $this->executeScheduleIntersectWith();
    }
    public function test_shceduleIntersectWith_concludedRequest_returnFalse()
    {
        $this->consultationRequest->concluded = true;
        $this->assertFalse($this->executeScheduleIntersectWith());
    }

    protected function executeReject()
    {
        $this->consultationRequest->reject();
    }
    public function test_reject_setStatusRejected()
    {
        $this->executeReject();
        $status = new ConsultationRequestStatusVO("rejected");
        $this->assertEquals($status, $this->consultationRequest->status);
    }
    public function test_reject_setConcludedFlagTrue()
    {
        $this->executeReject();
        $this->assertTrue($this->consultationRequest->concluded);
    }
    public function test_reject_alreadyConcluded_throwEx()
    {
        $this->consultationRequest->concluded = true;
        $operation = function () {
            $this->executeReject();
        };
        $errorDetail = 'forbidden: consultation request already concluded';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_reject_logActivity()
    {
        $this->executeReject();
        $this->assertEquals(1, $this->consultationRequest->consultationRequestActivityLogs->count());
        $this->assertInstanceOf(ConsultationRequestActivityLog::class, $this->consultationRequest->consultationRequestActivityLogs->first());
    }
    public function test_reject_recordConsultationRequestRejectedEvent()
    {
        $event = new CommonEvent(EventList::CONSULTATION_REQUEST_REJECTED, $this->consultationRequest->id);
        $this->executeReject();
        $this->assertEquals($event, $this->consultationRequest->recordedEvents[0]);
    }
    

    protected function executeOffer()
    {
        $this->consultationRequest->offer($this->getConsultationRequestData());
    }
    public function test_offer_setStatusOffered()
    {
        $this->executeOffer();
        $status = new ConsultationRequestStatusVO("offered");
        $this->assertEquals($status, $this->consultationRequest->status);
    }
    public function test_offer_alreadyConcluded_throwEx()
    {
        $this->consultationRequest->concluded = true;
        $operation = function () {
            $this->executeOffer();
        };
        $errorDetail = 'forbidden: consultation request already concluded';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_offer_changeProperties()
    {
        $this->consultationSetup->expects($this->once())
                ->method('getSessionStartEndTimeOf')
                ->with($this->startTime)
                ->willReturn($this->startEndTime);
        $this->executeOffer();
        $this->assertEquals($this->startEndTime, $this->consultationRequest->startEndTime);
        $channel = new ConsultationChannel($this->media, $this->address);
        $this->assertEquals($channel, $this->consultationRequest->channel);
    }
    public function test_offer_emptyStartTime_badRequest()
    {
        $this->startTime = null;
        $operation = function (){
            $this->executeOffer();
        };
        $errorDetail = "bad request: consultation request start time is mandatory";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_offer_startEndTimeUnavailableInParticipantsSchedule_throwEx()
    {
        $this->participant->expects($this->once())
                ->method('hasConsultationSessionInConflictWithConsultationRequest')
                ->with($this->consultationRequest)
                ->willReturn(true);
        $operation = function () {
            $this->executeOffer();
        };
        $errorDetail = 'forbidden: consultation request time in conflict with participan existing consultation session';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_offer_logActivity()
    {
        $this->executeOffer();
        $this->assertEquals(1, $this->consultationRequest->consultationRequestActivityLogs->count());
        $this->assertInstanceOf(ConsultationRequestActivityLog::class, $this->consultationRequest->consultationRequestActivityLogs->first());
    }
    public function test_offer_recordConsultationRequestOfferedEvent()
    {
        $this->executeOffer();
        $event = new CommonEvent(EventList::CONSULTATION_REQUEST_OFFERED, $this->consultationRequest->id);
        $this->assertEquals($event, $this->consultationRequest->recordedEvents[0]);
    }

    protected function executeAccept()
    {
        $this->consultationRequest->accept();
    }
    public function test_accept_alreadyConcluded_throwEx()
    {
        $this->consultationRequest->concluded = true;
        $operation = function () {
            $this->executeAccept();
        };
        $errorDetail = 'forbidden: consultation request already concluded';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_accept_setStatusScheduled()
    {
        $this->executeAccept();
        $status = new ConsultationRequestStatusVO("scheduled");
        $this->assertEquals($status, $this->consultationRequest->status);
    }
    public function test_accept_setConcludedFlagTrue()
    {
        $this->executeAccept();
        $this->assertTrue($this->consultationRequest->concluded);
    }
    public function test_accept_currentStatusNotProposed_error403()
    {
        $this->consultationRequest->status = new ConsultationRequestStatusVO('offered');
        $operation = function (){
            $this->executeAccept();
        };
        $errorDetail = "forbidden: can only accept proposed consultation request";
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    
    public function test_createConsultationSetupSchedule_returnConsultationSetupSchedule()
    {
        $id = 'id';
        $this->assertInstanceOf(ConsultationSession::class, $this->consultationRequest->createConsultationSession($id));
    }

    protected function executeIsOfferedConsultationRequestConflictedWith()
    {
        return $this->consultationRequest->isOfferedConsultationRequestConflictedWith($this->otherConsultationRequest);
    }
    public function test_isOfferedConsultaionRequestConflictedWith_noConflict_returnFalse()
    {
        $this->assertFalse($this->executeIsOfferedConsultationRequestConflictedWith());
    }
    public function test_isOfferedConsultationRequestConflictedWith_timeIntersectWithOther_returnTrue()
    {
        $this->consultationRequest->status = new ConsultationRequestStatusVO('offered');
        $this->startEndTime->expects($this->once())
                ->method('intersectWith')
                ->willReturn(true);
        $this->assertTrue($this->executeIsOfferedConsultationRequestConflictedWith());
    }
    public function test_isOfferedConsultationRequestConflictedWith_statusNotOffered_returnFalse()
    {
        $this->startEndTime->expects($this->any())
                ->method('intersectWith')
                ->willReturn(true);
        $this->assertFalse($this->executeIsOfferedConsultationRequestConflictedWith());
    }
    public function test_isOfferedConsultationRequestConflictedWith_comparedToSelf_returnFalse()
    {
        $this->consultationRequest->status = new ConsultationRequestStatusVO('offered');
        $this->startEndTime->expects($this->any())
                ->method('intersectWith')
                ->willReturn(true);
        $this->assertFalse($this->consultationRequest->isOfferedConsultationRequestConflictedWith($this->consultationRequest));
    }
}

class TestableConsultationRequest extends ConsultationRequest
{

    public $consultationSetup, $id = "consultationRequestId", $participant, $programConsultant, $startEndTime, $concluded = false, $status;
    public $channel;
    public $consultationRequestActivityLogs;
    public $recordedEvents;

    function __construct()
    {
        parent::__construct();
    }

}
