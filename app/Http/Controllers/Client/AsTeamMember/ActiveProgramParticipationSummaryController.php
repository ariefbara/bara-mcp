<?php

namespace App\Http\Controllers\Client\AsTeamMember;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use Query\Application\Service\Firm\Client\AsTeamMember\ViewAllActiveProgramParticipationSummary;
use Query\Domain\Model\Firm\Team\Member;

class ActiveProgramParticipationSummaryController extends AsTeamMemberBaseController
{
    public function showAll($teamId)
    {
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $result = (new ViewAllActiveProgramParticipationSummary($teamMemberRepository, $this->buildDataFinder()))
                ->execute($this->clientId(), $teamId, $this->getPage(), $this->getPageSize());
        return $this->listQueryResponse($result);
    }
}
