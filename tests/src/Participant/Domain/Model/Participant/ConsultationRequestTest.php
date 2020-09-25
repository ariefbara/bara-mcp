<?php

namespace Participant\Domain\Model\Participant;

use DateTimeImmutable;
use Participant\Domain\ {
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    Model\Participant
};
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\Model\SharedEntity\ConsultationRequestStatusVO;
use Tests\TestBase;

class ConsultationRequestTest extends TestBase
{

    protected $consultationSetup, $participant, $consultant;
    protected $consultationRequest;
    protected $id = 'negotiate-consultationSetupSchedule-id', $startTime;
    protected $startEndTime;
    protected $otherConsultationRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->startTime = new DateTimeImmutable('+24 hours');
        $this->startEndTime = $this->buildMockOfClass(DateTimeInterval::class);

        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);

        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->consultant = $this->buildMockOfClass(Consultant::class);

        $this->consultationRequest = new TestableConsultationRequest(
                $this->participant, 'id', $this->consultationSetup, $this->consultant, $this->startTime);
        $this->consultationRequest->startEndTime = $this->startEndTime;

        $this->otherConsultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
    }

    protected function executeConstruct()
    {
        $this->consultationSetup->expects($this->any())
                ->method('getSessionStartEndTimeOf')
                ->with($this->startTime)
                ->willReturn($this->startEndTime);
        return new TestableConsultationRequest($this->participant, $this->id, $this->consultationSetup,
                $this->consultant, $this->startTime);
    }

    public function test_construct_setProperties()
    {
        $consultationRequest = $this->executeConstruct();
        $this->assertEquals($this->participant, $consultationRequest->participant);
        $this->assertEquals($this->id, $consultationRequest->id);
        $this->assertEquals($this->consultationSetup, $consultationRequest->consultationSetup);
        $this->assertEquals($this->consultant, $consultationRequest->consultant);
        $this->assertEquals($this->startEndTime, $consultationRequest->startEndTime);
        $this->assertFalse($consultationRequest->concluded);

        $status = new ConsultationRequestStatusVO('proposed');
        $this->assertEquals($status, $consultationRequest->status);
    }

    public function test_construct_consultantHasConsultationSessionConflictedWithStartEndTime_throwEx()
    {
        $this->consultant->expects($this->once())
                ->method('hasConsultationSessionConflictedWith')
                ->willReturn(true);
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = "conflict: consultant already has consultation session at this time";
        $this->assertRegularExceptionThrowed($operation, "Conflict", $errorDetail);
    }

    protected function executeRePropose()
    {
        $this->consultationSetup->expects($this->any())
                ->method('getSessionStartEndTimeOf')
                ->with($this->startTime)
                ->willReturn($this->startEndTime);
        $this->consultationRequest->rePropose($this->startTime);
    }

    public function test_rePropose_changeStartEndTime()
    {
        $this->executeRePropose();
        $this->assertEquals($this->startEndTime, $this->consultationRequest->startEndTime);
    }

    public function test_repropose_consultantHasConsultationSessionConflictedWithStartEndTime_throwEx()
    {
        $this->consultant->expects($this->once())
                ->method('hasConsultationSessionConflictedWith')
                ->with($this->consultationRequest)
                ->willReturn(true);
        $operation = function () {
            $this->executeRePropose();
        };
        $errorDetail = "conflict: consultant already has consultation session at this time";
        $this->assertRegularExceptionThrowed($operation, "Conflict", $errorDetail);
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

    protected function executeAccept()
    {
        $this->consultationRequest->status = new ConsultationRequestStatusVO("offered");
        $this->consultationRequest->accept();
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
            $this->consultationRequest->accept();
        };
        $errorDetail = 'forbidden: request only valid for offered consultation request';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }

    public function test_createConsultationSetupSchedule_returnConsultationSetupSchedule()
    {
        $this->consultationRequest->startEndTime = $this->startEndTime;
        $consultationSession = new ConsultationSession(
                $this->consultationRequest->participant, $id = 'consultationSetupSchedule-id',
                $this->consultationRequest->consultationSetup, $this->consultationRequest->consultant,
                $this->consultationRequest->startEndTime
        );

        $this->assertEquals($consultationSession, $this->consultationRequest->createConsultationSession($id));
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

}

class TestableConsultationRequest extends ConsultationRequest
{

    public $consultationSetup, $id, $participant, $consultant, $startEndTime, $concluded, $status;

}
