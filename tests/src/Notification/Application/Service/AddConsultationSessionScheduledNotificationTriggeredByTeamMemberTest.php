<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Program\Participant\ConsultationSession;
use Tests\TestBase;

class AddConsultationSessionScheduledNotificationTriggeredByTeamMemberTest extends TestBase
{

    protected $consultationSessionRepository, $consultationSession;
    protected $memberRepository;
    protected $service;
    protected $memberId = "memberId", $consultationSessionId = "consultationSessionId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultationSessionRepository = $this->buildMockOfInterface(ConsultationSessionRepository::class);
        $this->consultationSessionRepository->expects($this->any())
                ->method("ofId")
                ->with($this->consultationSessionId)
                ->willReturn($this->consultationSession);

        $this->memberRepository = $this->buildMockOfInterface(MemberRepository::class);

        $this->service = new AddConsultationSessionScheduledNotificationTriggeredByTeamMember(
                $this->consultationSessionRepository, $this->memberRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->memberId, $this->consultationSessionId);
    }
    public function test_execute_addConsultationSessionAddAcceptNotificationTriggeredByTeamMember()
    {
        $this->consultationSession->expects($this->once())
                ->method("addAcceptNotificationTriggeredByTeamMember");
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->consultationSessionRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }

}
