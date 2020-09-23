<?php

namespace Query\Application\Auth\Firm\Team;

use Resources\Exception\RegularException;

class TeamMemberAuthorization
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

    public function execute(string $firmId, string $teamId, string $clientId): void
    {
        if (!$this->memberRepository->containRecordOfActiveTeamMemberCorrespondWithClient(
                        $firmId, $teamId, $clientId)
        ) {
            $errorDetail = "forbidden: only active team member can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
