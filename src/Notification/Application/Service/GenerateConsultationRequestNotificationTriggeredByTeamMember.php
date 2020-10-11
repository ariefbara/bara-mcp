<?php

namespace Notification\Application\Service;

class GenerateConsultationRequestNotificationTriggeredByTeamMember
{

    /**
     *
     * @var ConsultationRequestRepository
     */
    protected $consultationRequestRepository;

    /**
     *
     * @var MemberRepository
     */
    protected $memberRepository;

    public function __construct(ConsultationRequestRepository $consultationRequestRepository,
            MemberRepository $memberRepository)
    {
        $this->consultationRequestRepository = $consultationRequestRepository;
        $this->memberRepository = $memberRepository;
    }

    public function execute(string $memberId, string $consultationRequestId, int $state): void
    {
        $submitter = $this->memberRepository->ofId($memberId);
        $this->consultationRequestRepository->ofId($consultationRequestId)
                ->createNotificationTriggeredByTeamMember($state, $submitter);
        $this->consultationRequestRepository->update();
    }

}
