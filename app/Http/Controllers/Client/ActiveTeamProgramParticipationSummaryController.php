<?php

namespace App\Http\Controllers\Client;

use Query\Application\Service\Firm\Client\ViewTeamMembership;
use Query\Domain\Model\Firm\Team\Member;

class ActiveTeamProgramParticipationSummaryController extends ClientBaseController
{

    public function showAll()
    {
        $teamMembershipRepository = $this->em->getRepository(Member::class);
        $memberships = (new ViewTeamMembership($teamMembershipRepository))
                ->showAll($this->firmId(), $this->clientId(), 1, 100, true);
        
        $dataFinder = $this->buildDataFinder();
        $result = [];
        $result['total'] = count($memberships);
        foreach ($memberships as $membership) {
            $result['list'][] = [
                "id" => $membership->getId(),
                "position" => $membership->getPosition(),
                "team" => [
                    "id" => $membership->getTeam()->getId(),
                    "name" => $membership->getTeam()->getName(),
                    "programParticipationSummaries" => 
                            $dataFinder->summaryOfAllTeamProgramParticipations($membership->getTeam()->getId(), 1, 100),
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }

}
