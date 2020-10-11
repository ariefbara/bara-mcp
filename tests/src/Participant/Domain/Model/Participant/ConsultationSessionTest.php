<?php

namespace Participant\Domain\Model\Participant;

use Config\EventList;
use Participant\Domain\ {
    DependencyModel\Firm\Client\TeamMembership,
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    DependencyModel\Firm\Team,
    Model\Participant,
    Model\Participant\ConsultationSession\ConsultationSessionActivityLog,
    Model\Participant\ConsultationSession\ParticipantFeedback
};
use Resources\Domain\ {
    Event\CommonEvent,
    ValueObject\DateTimeInterval
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ConsultationSessionTest extends TestBase
{

    protected $consultationSetup, $participant, $consultant;
    protected $id = 'consultationSession-id', $startEndTime;
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
        $this->startEndTime = $this->buildMockOfClass(DateTimeInterval::class);

        $this->consultationSession = new TestableConsultationSession(
                $this->participant, 'consultationSession-id', $this->consultationSetup, $this->consultant,
                $this->startEndTime);
        
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);

        $this->participantFeedback = $this->buildMockOfClass(ParticipantFeedback::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
        $this->team = $this->buildMockOfClass(Team::class);
    }

    protected function executeConstruct()
    {
        return new TestableConsultationSession(
                $this->participant, $this->id, $this->consultationSetup, $this->consultant, $this->startEndTime);
    }
    public function test_construct_setProperties()
    {
        $consultationSession = $this->executeConstruct();
        $this->assertEquals($this->consultationSetup, $consultationSession->consultationSetup);
        $this->assertEquals($this->id, $consultationSession->id);
        $this->assertEquals($this->participant, $consultationSession->participant);
        $this->assertEquals($this->consultant, $consultationSession->consultant);
        $this->assertEquals($this->startEndTime, $consultationSession->startEndTime);
    }
    public function test_construct_recordOfferedConsultationSessionAccepetedEvent()
    {
        $consultationSession = $this->executeConstruct();
        $event = new CommonEvent(EventList::OFFERED_CONSULTATION_REQUEST_ACCEPTED, $this->id);
        $this->assertEquals($event, $consultationSession->recordedEvents[0]);
    }
    
    public function test_belongsToTeam_returnParticipantBelongsToTeamResult()
    {
        $this->participant->expects($this->once())
                ->method("belongsToTeam")
                ->willReturn(true);
        $this->assertTrue($this->consultationSession->belongsToTeam($this->team));
    }

    public function test_conflictedWithConsultationRequest_returnConsultationRequestsScheduleIntersectWithResult()
    {
        $this->consultationRequest->expects($this->once())
                ->method("scheduleIntersectWith")
                ->with($this->consultationSession->startEndTime)
                ->willReturn(true);

        $this->assertTrue($this->consultationSession->conflictedWithConsultationRequest($this->consultationRequest));
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
        $this->assertInstanceOf(ConsultationSessionActivityLog::class, $this->consultationSession->consultationSessionActivityLogs->first());
    }
    
}

class TestableConsultationSession extends ConsultationSession
{
    public $recordedEvents;
    public $consultationSetup, $id, $participant, $consultant, $startEndTime;
    public $participantFeedback;
    public $consultationSessionActivityLogs;
}
