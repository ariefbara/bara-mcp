<?php

namespace Team\Application\Service\Team;

class RemoveMember
{
    /**
     *
     * @var MemberRepository
     */
    protected $memberRepository;
    
    public function __construct(MemberRepository $memberRepository)
    {
        $this->memberRepository = $memberRepository;
    }
    
    public function execute(string $firmId, string $teamId, string $clientId, string $memberId): void
    {
        $other = $this->memberRepository->ofId($firmId, $teamId, $memberId);
        $this->memberRepository->aMemberCorrespondWithClient($firmId, $teamId, $clientId)
                ->removeOtherMember($other);
        $this->memberRepository->update();
    }

}
