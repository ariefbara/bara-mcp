<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\{
    Program\Participant\ConsultationRequest,
    Team\Member
};
use Tests\TestBase;

class GenerateConsultationRequestNotificationTriggeredByTeamMemberTest extends TestBase
{

    protected $consultationRequestRepository, $consultationRequest;
    protected $memberRepository, $member;
    protected $service;
    protected $memberId = "memberId", $consultationRequestId = "consultationRequestId", $state = 1;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultationRequestRepository = $this->buildMockOfInterface(ConsultationRequestRepository::class);
        $this->consultationRequestRepository->expects($this->any())
                ->method("ofId")
                ->with($this->consultationRequestId)
                ->willReturn($this->consultationRequest);

        $this->member = $this->buildMockOfClass(Member::class);
        $this->memberRepository = $this->buildMockOfInterface(MemberRepository::class);
        $this->memberRepository->expects($this->any())
                ->method("ofId")
                ->with($this->memberId)
                ->willReturn($this->member);

        $this->service = new GenerateConsultationRequestNotificationTriggeredByTeamMember(
                $this->consultationRequestRepository, $this->memberRepository);
    }

    protected function execute()
    {
        $this->service->execute($this->memberId, $this->consultationRequestId, $this->state);
    }

    public function test_execute_createConsultationRequestsNotificationTriggeredByTeamMember()
    {
        $this->consultationRequest->expects($this->once())
                ->method("createNotificationTriggeredByTeamMember")
                ->with($this->state, $this->member);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->consultationRequestRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }

}
