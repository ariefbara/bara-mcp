<?php

namespace App\Http\Controllers\Client\AsTeamMember;

use App\Http\Controllers\Client\ClientBaseController;
use Firm\Domain\Model\Firm\Team\Member as Member2;
use Participant\Application\Service\Client\AsTeamMember\ExecuteTeamParticipantTask;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership as TeamMemberInParticipantBC;
use Participant\Domain\Model\ITaskExecutableByParticipant;
use Participant\Domain\Model\TeamProgramParticipation as TeamParticipantInParticipantBC;
use Query\Application\Auth\Firm\Team\TeamAdminAuthorization;
use Query\Application\Auth\Firm\Team\TeamMemberAuthorization;
use Query\Application\Service\Client\TeamMember\ExecuteParticipantTask;
use Query\Application\Service\Client\TeamMember\ExecuteProgramTask;
use Query\Application\Service\Client\TeamMember\ExecuteTeamParticipantQueryTask;
use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant as ITaskExecutableByParticipant2;
use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByParticipant;
use Query\Domain\Model\Firm\Team\Member;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Task\Participant\ParticipantQueryTask;

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

    protected function executeTeamParticipantQueryTask(
            string $teamId, string $teamParticipantId, ITaskExecutableByParticipant2 $task): void
    {
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $teamParticipantRepository = $this->em->getRepository(TeamProgramParticipation::class);
        (new ExecuteParticipantTask($teamMemberRepository, $teamParticipantRepository))
                ->execute($this->firmId(), $this->clientId(), $teamId, $teamParticipantId, $task);
    }

    protected function executeQueryInProgramTaskExecutableByTeamParticipant(
            string $teamId, string $teamParticipantId, ITaskInProgramExecutableByParticipant $task): void
    {
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $teamParticipantRepository = $this->em->getRepository(TeamProgramParticipation::class);
        (new ExecuteProgramTask($teamMemberRepository, $teamParticipantRepository))
                ->execute($this->firmId(), $this->clientId(), $teamId, $teamParticipantId, $task);
    }
    
    protected function executeTeamParticipantExtentedQueryTask(
            string $teamId, string $teamParticipantId, ParticipantQueryTask $task, $payload): void
    {
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $teamParticipantRepository = $this->em->getRepository(TeamProgramParticipation::class);
        (new ExecuteTeamParticipantQueryTask($teamMemberRepository, $teamParticipantRepository))
                ->execute($this->firmId(), $this->clientId(), $teamId, $teamParticipantId, $task, $payload);
    }
    
    protected function executeTeamParticipantExtendedTask(
            $teamId, $teamProgramParticipationId, \Participant\Domain\Task\Participant\ParticipantTask $task, $payload): void
    {
        $teamMemberRepository = $this->em->getRepository(TeamMemberInParticipantBC::class);
        $teamParticipantRepository = $this->em->getRepository(TeamParticipantInParticipantBC::class);
        (new \Participant\Application\Service\Client\AsTeamMember\ExecuteParticipantTask($teamMemberRepository, $teamParticipantRepository))
                ->execute($this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $task, $payload);
    }

}
