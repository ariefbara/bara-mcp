<?php

namespace App\Http\Controllers\Personnel;

use Query\Application\Service\Firm\Program\ViewTeamMember;
use Query\Domain\Model\Firm\Team\Member;

class TeamMemberController extends PersonnelBaseController
{
    public function showAll($teamId)
    {
        $this->authorizedRequestFromActivePersonnel();
        $activeStatus = $this->filterBooleanOfQueryRequest("activeStatus");
        
        $teamMembers = $this->buildViewService()
                ->showAll($this->firmId(), $teamId, $this->getPage(), $this->getPageSize(), $activeStatus);
        
        $result = [];
        $result["total"] = count($teamMembers);
        foreach ($teamMembers as $teamMember) {
            $result["list"][] = $this->arrayDataOfTeamMember($teamMember);
        }
        return $this->listQueryResponse($result);
    }
    public function show($teamMemberId)
    {
        $this->authorizedRequestFromActivePersonnel();
        $teamMember = $this->buildViewService()->showById($this->firmId(), $teamMemberId);
        return $this->singleQueryResponse($this->arrayDataOfTeamMember($teamMember));
    }
    
    protected function arrayDataOfTeamMember(Member $teamMember): array
    {
        return [
            "id" => $teamMember->getId(),
            "active" => $teamMember->isActive(),
            "anAdmin" => $teamMember->isAnAdmin(),
            "position" => $teamMember->getPosition(),
            "joinTime" => $teamMember->getJoinTimeString(),
            "client" => [
                "id" => $teamMember->getClient()->getId(),
                "name" => $teamMember->getClient()->getFullName(),
            ],
        ];
    }
    protected function buildViewService()
    {
        $teamMemberRepository = $this->em->getRepository(Member::class);
        return new ViewTeamMember($teamMemberRepository);
    }
}
