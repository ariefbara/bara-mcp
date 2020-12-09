<?php

namespace Participant\Domain\Model\Participant;

use Config\EventList;
use DateTimeImmutable;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\DependencyModel\Firm\Team;
use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\ConsultationRequest\ConsultationRequestActivityLog;
use Resources\Domain\Event\CommonEvent;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\Model\SharedEntity\ConsultationRequestStatusVO;
use SharedContext\Domain\ValueObject\ConsultationChannel;
use Tests\TestBase;

class ConsultationRequestTest extends TestBase
{

    protected $consultationSetup, $participant, $consultant;
    protected $ineligibleConsultant;
    protected $teamMember;
    protected $consultationRequest;
    protected $id = 'negotiate-consultationSetupSchedule-id', $startTime;
    protected $startEndTime;
    protected $media = "new media", $address = "new address";
    protected $otherConsultationRequest;
    
    protected $team;

    protected function setUp(): void
    {
        parent::setUp();
        $this->startTime = new DateTimeImmutable('+24 hours');
        $this->startEndTime = $this->buildMockOfClass(DateTimeInterval::class);

        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);

        $this->participant = $this->buildMockOfClass(Participant::class);
        
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultant->expects($this->any())->method('canAcceptConsultationRequest')->willReturn(true);
        
        $this->ineligibleConsultant = $this->buildMockOfClass(Consultant::class);
        $this->ineligibleConsultant->expects($this->any())->method('canAcceptConsultationRequest')->willReturn(false);

        $consultationRequestData = new ConsultationRequestData(new \DateTimeImmutable(), "media", "address");
        $this->consultationRequest = new TestableConsultationRequest(
                $this->participant, 'id', $this->consultationSetup, $this->consultant, $consultationRequestData, null);
        $this->consultationRequest->startEndTime = $this->startEndTime;
        $this->consultationRequest->consultationRequestActivityLogs->clear();
        $this->consultationRequest->recordedEvents = [];

        $this->otherConsultationRequest = new TestableConsultationRequest(
                $this->participant, "otherId", $this->consultationSetup, $this->consultant, $consultationRequestData, null);
        
        
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
        $this->team = $this->buildMockOfClass(Team::class);
    }
    
    protected function getConsultationRequestData()
    {
        return new ConsultationRequestData($this->startTime, $this->media, $this->address);
    }
    
    protected function executeConstruct()
    {
        $this->consultationSetup->expects($this->any())
                ->method('getSessionStartEndTimeOf')
                ->with($this->startTime)
                ->willReturn($this->startEndTime);
        return new TestableConsultationRequest($this->participant, $this->id, $this->consultationSetup,
                $this->consultant, $this->getConsultationRequestData(), $this->teamMember);
    }
    public function test_construct_setProperties()
    {
        $consultationRequest = $this->executeConstruct();
        $this->assertEquals($this->participant, $consultationRequest->participant);
        $this->assertEquals($this->id, $consultationRequest->id);
        $this->assertEquals($this->consultationSetup, $consultationRequest->consultationSetup);
        $this->assertEquals($this->consultant, $consultationRequest->consultant);
        $this->assertEquals($this->startEndTime, $consultationRequest->startEndTime);
        $channel = new ConsultationChannel($this->media, $this->address);
        $this->assertEquals($channel, $consultationRequest->channel);
        $this->assertFalse($consultationRequest->concluded);

        $status = new ConsultationRequestStatusVO('proposed');
        $this->assertEquals($status, $consultationRequest->status);
    }
    public function test_construct_emptyStartTime_badRequest()
    {
        $this->startTime = null;
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: consultation request start time is mandatory";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_consultantCantAcceptRequest_throwEx()
    {
        $operation = function () {
            new TestableConsultationRequest($this->participant, $this->id, $this->consultationSetup,
                    $this->ineligibleConsultant, $this->getConsultationRequestData(), $this->teamMember);
        };
        $errorDetail = "forbidden: consultant can accept consultation request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_construct_addConsultationRequestActivityLog()
    {
        $consultationRequest = $this->executeConstruct();
        $this->assertInstanceOf(ConsultationRequestActivityLog::class, $consultationRequest->consultationRequestActivityLogs->first());
    }
    public function test_construct_recordConsultationRequestSubmittedEvent()
    {
        $consultationRequest = $this->executeConstruct();
        $event = new CommonEvent(EventList::CONSULTATION_REQUEST_SUBMITTED, $this->id);
        $this->assertEquals($event, $consultationRequest->recordedEvents[0]);
    }
    
     public function test_belongsToTeam_returnParticipantBelongsToTeamResult()
    {
        $this->participant->expects($this->once())
                ->method("belongsToTeam")
                ->willReturn(true);
        $this->assertTrue($this->consultationRequest->belongsToTeam($this->team));
    }
    
    public function test_scheduleIntersectWith_returnStartEndTimeIntersectResult()
    {
        $this->startEndTime->expects($this->once())
                ->method("intersectWith")
                ->with($otherStartEndTime = $this->buildMockOfClass(DateTimeInterval::class));
        $this->consultationRequest->scheduleIntersectWith($otherStartEndTime);
    }
    
    protected function executeIsProposedConsultationRequestConflictedWith()
    {
        return $this->consultationRequest->isProposedConsultationRequestConflictedWith($this->otherConsultationRequest);
    }
    public function test_isProposedConsultaionRequestConflictedWith_noConflict_returnFalse()
    {
        $this->assertFalse($this->executeIsProposedConsultationRequestConflictedWith());
    }
    public function test_isProposedConsultationRequestConflictedWith_timeIntersectWithOther_returnTrue()
    {
        $this->startEndTime->expects($this->once())
                ->method('intersectWith')
                ->willReturn(true);
        $this->assertTrue($this->executeIsProposedConsultationRequestConflictedWith());
    }
    public function test_isProposedConsultationRequestConflictedWith_statusNotProposed_returnFalse()
    {
        $this->startEndTime->expects($this->any())
                ->method('intersectWith')
                ->willReturn(true);
        $this->consultationRequest->status = new ConsultationRequestStatusVO('cancelled');
        $this->assertFalse($this->executeIsProposedConsultationRequestConflictedWith());
    }
    public function test_isProposedConsultationRequestConflictedWith_comparedToSelf_returnFalse()
    {
        $this->startEndTime->expects($this->any())
                ->method('intersectWith')
                ->willReturn(true);
        $this->assertFalse($this->consultationRequest->isProposedConsultationRequestConflictedWith($this->consultationRequest));
    }

    protected function executeRePropose()
    {
        $this->consultationSetup->expects($this->any())
                ->method('getSessionStartEndTimeOf')
                ->with($this->startTime)
                ->willReturn($this->startEndTime);
        $this->consultationRequest->rePropose($this->getConsultationRequestData(), $this->teamMember);
    }
    public function test_rePropose_changeProperties()
    {
        $this->executeRePropose();
        $this->assertEquals($this->startEndTime, $this->consultationRequest->startEndTime);
        $channel = new ConsultationChannel($this->media, $this->address);
        $this->assertEquals($channel, $this->consultationRequest->channel);
    }
    public function test_repropose_consultantCanAcceptRequest_throwEx()
    {
        $this->consultationRequest->consultant = $this->ineligibleConsultant;
        $operation = function () {
            $this->executeRePropose();
        };
        $errorDetail = "forbidden: consultant can accept consultation request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_rePropose_alreadyConcluded_throwEx()
    {
        $this->consultationRequest->concluded = true;
        $operation = function () {
            $this->executeRePropose();
        };
        $errorDetail = 'forbidden: consultation request already concluded';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_rePropose_setStatusProposed()
    {
        $this->consultationRequest->status = new ConsultationRequestStatusVO("offered");
        $this->executeRePropose();

        $this->assertEquals(new ConsultationRequestStatusVO("proposed"), $this->consultationRequest->status);
    }
    public function test_rePropose_addActivityLog()
    {
        $this->executeRePropose();
        $this->assertInstanceOf(ConsultationRequestActivityLog::class, $this->consultationRequest->consultationRequestActivityLogs->first());
    }
    public function test_rePropose_recordConsultationRequestTimeChangedEvent()
    {
        $this->executeRePropose();
        $event = new CommonEvent(EventList::CONSULTATION_REQUEST_TIME_CHANGED, $this->consultationRequest->id);
        $this->assertEquals($event, $this->consultationRequest->recordedEvents[0]);
    }

    protected function executeCancel()
    {
        $this->consultationRequest->cancel();
    }
    public function test_cancel_setStatusCancelled()
    {
        $this->executeCancel();
        $this->assertEquals(new ConsultationRequestStatusVO("cancelled"), $this->consultationRequest->status);
    }
    public function test_cancel_setConcludedFlagTrue()
    {
        $this->executeCancel();
        $this->assertTrue($this->consultationRequest->concluded);
    }
    public function test_cancel_alreadyConcluded_throwEx()
    {
        $this->consultationRequest->concluded = true;
        $operation = function () {
            $this->executeCancel();
        };
        $errorDetail = 'forbidden: consultation request already concluded';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_cancel_addActivityLog()
    {
        $this->executeCancel();
        $this->assertInstanceOf(ConsultationRequestActivityLog::class, $this->consultationRequest->consultationRequestActivityLogs->first());
    }
    public function test_cancel_recordCancelledEvent()
    {
        $this->executeCancel();
        $event = new CommonEvent(EventList::CONSULTATION_REQUEST_CANCELLED, $this->consultationRequest->id);
        $this->assertEquals($event, $this->consultationRequest->recordedEvents[0]);
    }

    protected function executeAccept()
    {
        $this->consultationRequest->status = new ConsultationRequestStatusVO("offered");
        $this->consultationRequest->accept($this->teamMember);
    }
    public function test_accept_setStatusConsultationSetupScheduled()
    {
        $this->executeAccept();
        $this->assertEquals(new ConsultationRequestStatusVO("scheduled"), $this->consultationRequest->status);
    }
    public function test_accept_setConcludedFlagTrue()
    {
        $this->executeAccept();
        $this->assertTrue($this->consultationRequest->concluded);
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
    public function test_accept_statusNotOffered_throwEx()
    {
        $operation = function () {
            $this->consultationRequest->accept($this->teamMember);
        };
        $errorDetail = 'forbidden: request only valid for offered consultation request';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }

    public function test_createConsultationSetupSchedule_returnConsultationSetupSchedule()
    {
        $this->consultant->expects($this->any())->method("isActive")->willReturn(true);
        $this->assertInstanceOf(ConsultationSession::class, $this->consultationRequest->createConsultationSession($id = "consultationSessionId", $this->teamMember));
    }
    
}

class TestableConsultationRequest extends ConsultationRequest
{

    public $consultationSetup, $id, $participant, $consultant, $startEndTime, $concluded, $status;
    public $channel;
    public $consultationRequestActivityLogs;
    
    public $recordedEvents;

}
