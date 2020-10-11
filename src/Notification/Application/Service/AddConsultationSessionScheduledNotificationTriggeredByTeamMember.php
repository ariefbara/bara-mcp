<?php

namespace Notification\Application\Service;

class AddConsultationSessionScheduledNotificationTriggeredByTeamMember
{
    /**
     *
     * @var ConsultationSessionRepository
     */
    protected $consultationSessionRepository;
    /**
     *
     * @var MemberRepository
     */
    protected $memberRepository;
    
    public function __construct(ConsultationSessionRepository $consultationSessionRepository,
            MemberRepository $memberRepository)
    {
        $this->consultationSessionRepository = $consultationSessionRepository;
        $this->memberRepository = $memberRepository;
    }
    
    public function execute(string $memberId, string $consultationSessionId): void
    {
        $teamMember = $this->memberRepository->ofId($memberId);
        $this->consultationSessionRepository->ofId($consultationSessionId)
                ->addAcceptNotificationTriggeredByTeamMember($teamMember);
        $this->consultationSessionRepository->update();
    }

}
