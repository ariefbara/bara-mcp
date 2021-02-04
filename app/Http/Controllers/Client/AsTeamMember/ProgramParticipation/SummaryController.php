<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use Query\Application\Service\Firm\Client\AsTeamMember\AsProgramParticipant\ViewSummary;
use Query\Domain\Model\Firm\Team\Member;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;

class SummaryController extends AsTeamMemberBaseController
{
    public function show($teamId, $teamProgramParticipationId)
    {
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $teamProgramParticipationRepository = $this->em->getRepository(TeamProgramParticipation::class);
        $result = (new ViewSummary($teamMemberRepository, $teamProgramParticipationRepository, $this->buildDataFinder()))
                ->execute($this->clientId(), $teamId, $teamProgramParticipationId);
        return $this->singleQueryResponse($result);
    }
}
