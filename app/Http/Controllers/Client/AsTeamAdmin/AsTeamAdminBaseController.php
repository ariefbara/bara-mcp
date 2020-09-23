<?php

namespace App\Http\Controllers\Client\AsTeamAdmin;

use App\Http\Controllers\Client\ClientBaseController;
use Query\ {
    Application\Auth\Firm\Team\TeamAdminAuthorization,
    Domain\Model\Firm\Team\Member
};

class AsTeamAdminBaseController extends ClientBaseController
{
    protected function authorizeClientIsActiveTeamMemberWithAdminPriviledge(string $teamId): void
    {
        $memberRepository = $this->em->getRepository(Member::class);
        $authZ = new TeamAdminAuthorization($memberRepository);
        $authZ->execute($this->firmId(), $teamId, $this->clientId());
    }
}
