<?php

namespace App\Http\Controllers\Client\AsTeamMember;

use App\Http\Controllers\Client\ClientBaseController;
use Firm\Domain\Model\Firm\Team\Member as Member2;
use Query\Application\Auth\Firm\Team\TeamAdminAuthorization;
use Query\Application\Auth\Firm\Team\TeamMemberAuthorization;
use Query\Domain\Model\Firm\Team\Member;

class AsTeamMemberBaseController extends ClientBaseController
{
    protected function authorizeClientIsActiveTeamMember(string $teamId): void
    {
        $memberRepository = $this->em->getRepository(Member::class);
        $authZ = new TeamMemberAuthorization($memberRepository);
        $authZ->execute($this->firmId(), $teamId, $this->clientId());
    }
    
    protected function authorizeClientIsActiveTeamMemberWithAdminPriviledge(string $teamId): void
    {
        $memberRepository = $this->em->getRepository(Member::class);
        $authZ = new TeamAdminAuthorization($memberRepository);
        $authZ->execute($this->firmId(), $teamId, $this->clientId());
    }
    
    protected function teamMemberQueryRepository()
    {
        return $this->em->getRepository(Member::class);
    }
    protected function teamMemberFirmRepository()
    {
        return $this->em->getRepository(Member2::class);
    }
}
