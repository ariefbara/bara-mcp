<?php

namespace Query\Application\Auth\Firm\Team;

use Resources\Exception\RegularException;

class TeamAdminAuthorization
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
        if (!$this->memberRepository->containRecordOfActiveTeamMemberWithAdminPriviledgeCorrespondWithClient(
                        $firmId, $teamId, $clientId)
        ) {
            $errorDetail = "forbidden: only team member with admin priviledge can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
