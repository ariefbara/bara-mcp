<?php

namespace Participant\Domain\Model\Participant;

use Config\EventList;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\DependencyModel\Firm\Team;
use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\ConsultationSession\ConsultationSessionActivityLog;
use Participant\Domain\Model\Participant\ConsultationSession\ParticipantFeedback;
use Resources\Domain\Event\CommonEvent;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use SharedContext\Domain\ValueObject\ConsultationChannel;
use Tests\TestBase;

class ConsultationSessionTest extends TestBase
{

    protected $consultationSetup, $participant, $consultant;
    protected $id = 'consultationSession-id', $startEndTime, $channel;
    protected $consultationSession;
    protected $consultationRequest;
    protected $participantFeedback;
    protected $formRecordData;
    protected $teamMember;
    protected $team;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultant->expects($this->any())->method("isActive")->willReturn(true);
        $this->startEndTime = $this->buildMockOfClass(DateTimeInterval::class);
        $this->channel = $this->buildMockOfClass(ConsultationChannel::class);
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);

        $this->consultationSession = new TestableConsultationSession(
                $this->participant, 'consultationSession-id', $this->consultationSetup, $this->consultant,
                $this->startEndTime, $this->channel, $this->teamMember);
        $this->consultationSession->consultationSessionActivityLogs->clear();

        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);

        $this->participantFeedback = $this->buildMockOfClass(ParticipantFeedback::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);

        $this->team = $this->buildMockOfClass(Team::class);
    }

    protected function executeConstruct()
    {
        return new TestableConsultationSession(
                $this->participant, $this->id, $this->consultationSetup, $this->consultant, $this->startEndTime,
                $this->channel, $this->teamMember);
    }
    public function test_construct_setProperties()
    {
        $consultationSession = $this->executeConstruct();
        $this->assertEquals($this->consultationSetup, $consultationSession->consultationSetup);
        $this->assertEquals($this->id, $consultationSession->id);
        $this->assertEquals($this->participant, $consultationSession->participant);
        $this->assertEquals($this->consultant, $consultationSession->consultant);
        $this->assertEquals($this->startEndTime, $consultationSession->startEndTime);
        $this->assertEquals($this->channel, $consultationSession->channel);
        $this->assertFalse($consultationSession->cancelled);
    }
    public function test_construct_recordOfferedConsultationSessionAccepetedEvent()
    {
        $consultationSession = $this->executeConstruct();
        $event = new CommonEvent(EventList::OFFERED_CONSULTATION_REQUEST_ACCEPTED, $this->id);
        $this->assertEquals($event, $consultationSession->recordedEvents[0]);
    }
    public function test_construct_addActivityLog()
    {
        $consultationSession = $this->executeConstruct();
        $this->assertEquals(1, $consultationSession->consultationSessionActivityLogs->count());
        $this->assertInstanceOf(ConsultationSessionActivityLog::class,
                $consultationSession->consultationSessionActivityLogs->first());
    }
    public function test_construct_inactiveConsultant_forbidden()
    {
        $operation = function () {
            $consultant = $this->buildMockOfClass(Consultant::class);
            $consultant->expects($this->any())->method("isActive")->willReturn(false);
            return new TestableConsultationSession(
                    $this->participant, $this->id, $this->consultationSetup, $consultant, $this->startEndTime, 
                    $this->channel, $this->teamMember);
        };
        $errorDetail = "forbidden: inactive mentor can't give consultation";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }

    public function test_belongsToTeam_returnParticipantBelongsToTeamResult()
    {
        $this->participant->expects($this->once())
                ->method("belongsToTeam")
                ->willReturn(true);
        $this->assertTrue($this->consultationSession->belongsToTeam($this->team));
    }

    protected function executeConflictedWithConsultationRequest()
    {
        $this->consultationRequest->expects($this->any())
                ->method("scheduleIntersectWith")
                ->willReturn(true);
        return $this->consultationSession->conflictedWithConsultationRequest($this->consultationRequest);
    }
    public function test_conflictedWithConsultationRequest_returnConsultationRequestsScheduleIntersectWithResult()
    {
        $this->consultationRequest->expects($this->once())
                ->method("scheduleIntersectWith")
                ->with($this->consultationSession->startEndTime)
                ->willReturn(true);
        $this->assertTrue($this->executeConflictedWithConsultationRequest());
    }
    public function test_conflictedWithConsultationRequest_cancelledSession_returnFalse()
    {
        $this->consultationSession->cancelled = true;
        $this->assertFalse($this->executeConflictedWithConsultationRequest());
    }

    protected function executeSetParticipantFeedback()
    {
        $this->consultationSession->setParticipantFeedback($this->formRecordData);
    }
    public function test_setParticipantFeedback_setParticipantFeedback()
    {
        $this->executeSetParticipantFeedback();
        $this->assertInstanceOf(ParticipantFeedback::class, $this->consultationSession->participantFeedback);
    }
    public function test_setParticipantFeedback_alreadyHasParticipantFeedback_updateExistingParticipantFeedback()
    {
        $this->consultationSession->participantFeedback = $this->participantFeedback;
        $this->participantFeedback->expects($this->once())
                ->method('update')
                ->with($this->formRecordData);
        $this->executeSetParticipantFeedback();
    }
    public function test_setParticipantFeedback_addActivityLog()
    {
        $this->executeSetParticipantFeedback();
        $this->assertEquals(1, $this->consultationSession->consultationSessionActivityLogs->count());
        $this->assertInstanceOf(ConsultationSessionActivityLog::class,
                $this->consultationSession->consultationSessionActivityLogs->first());
    }
    public function test_setParticipantFeedback_cancelledSession_forbidden()
    {
        $this->consultationSession->cancelled = true;
        $operation = function () {
            $this->executeSetParticipantFeedback();
        };
        $errorDetail = "forbidden: can send report on cancelled session";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }

}

class TestableConsultationSession extends ConsultationSession
{

    public $recordedEvents;
    public $consultationSetup, $id, $participant, $consultant, $startEndTime;
    public $channel;
    public $cancelled = false;
    public $participantFeedback;
    public $consultationSessionActivityLogs;

}
