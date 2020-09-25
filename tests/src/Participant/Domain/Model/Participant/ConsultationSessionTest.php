<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\ {
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    Model\Participant,
    Model\Participant\ConsultationSession\ParticipantFeedback
};
use Resources\Domain\ValueObject\DateTimeInterval;
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
    }

    public function test_construct_setProperties()
    {
        $consultationSession = new TestableConsultationSession(
                $this->participant, $this->id, $this->consultationSetup, $this->consultant, $this->startEndTime);
        $this->assertEquals($this->consultationSetup, $consultationSession->consultationSetup);
        $this->assertEquals($this->id, $consultationSession->id);
        $this->assertEquals($this->participant, $consultationSession->participant);
        $this->assertEquals($this->consultant, $consultationSession->consultant);
        $this->assertEquals($this->startEndTime, $consultationSession->startEndTime);
    }

    public function test_conflictedWithConsultationRequest_returnStartEndTimeComparisonResultWithConsultationRequestStartEndTime()
    {
        $startEndTime = $this->buildMockOfClass(DateTimeInterval::class);
        $this->consultationRequest->expects($this->once())
                ->method('getStartEndTime')
                ->willReturn($startEndTime);

        $this->startEndTime->expects($this->once())
                ->method('intersectWith')
                ->with($startEndTime)
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

}

class TestableConsultationSession extends ConsultationSession
{

    public $consultationSetup, $id, $participant, $consultant, $startEndTime;
    public $participantFeedback;

}
