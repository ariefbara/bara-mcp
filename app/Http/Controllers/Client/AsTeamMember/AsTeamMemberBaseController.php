<?php

namespace App\Http\Controllers\Client\AsTeamMember;

use App\Http\Controllers\Client\ClientBaseController;
use Firm\Domain\Model\Firm\Team\Member as Member2;
use Participant\Application\Service\Client\AsTeamMember\ExecuteTeamParticipantTask;
use Participant\Domain\Model\ITaskExecutableByParticipant;
use Query\Application\Auth\Firm\Team\TeamAdminAuthorization;
use Query\Application\Auth\Firm\Team\TeamMemberAuthorization;
use Query\Domain\Model\Firm\Team\Member;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership as TeamMemberInParticipantBC;
use Participant\Domain\Model\TeamProgramParticipation as TeamParticipantInParticipantBC;

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

    protected function executeTeamParticipantTask(
            string $teamId, string $teamParticipantId, ITaskExecutableByParticipant $task): void
    {
        $teamMemberRepository = $this->em->getRepository(TeamMemberInParticipantBC::class);
        $teamParticipantRepository = $this->em->getRepository(TeamParticipantInParticipantBC::class);
        (new ExecuteTeamParticipantTask($teamMemberRepository, $teamParticipantRepository))
                ->execute($this->firmId(), $this->clientId(), $teamId, $teamParticipantId, $task);
    }

}
