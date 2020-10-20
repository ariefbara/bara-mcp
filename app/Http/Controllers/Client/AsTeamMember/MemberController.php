<?php

namespace App\Http\Controllers\Client\AsTeamMember;

use Query\{
    Application\Service\Firm\Team\ViewMember,
    Domain\Model\Firm\Team\Member
};
use Team\{
    Application\Service\Team\AddMember,
    Application\Service\Team\RemoveMember,
    Domain\DependencyModel\Firm\Client,
    Domain\Model\Team\Member as Member2
};

class MemberController extends AsTeamMemberBaseController
{

    public function add($teamId)
    {
        $service = $this->buildAddService();
        $clientIdToBeAddedAsMember = $this->stripTagsInputRequest("clientId");
        $anAdmin = $this->filterBooleanOfInputRequest("anAdmin");
        $memberPosition = $this->stripTagsInputRequest("memberPosition");
        $memberId = $service->execute(
                $this->firmId(), $teamId, $this->clientId(), $clientIdToBeAddedAsMember, $anAdmin, $memberPosition);
        
        return $this->show($teamId, $memberId);
    }

    public function remove($teamId, $memberId)
    {
        $service = $this->buildRemoveService();
        $service->execute($this->firmId(), $teamId, $this->clientId(), $memberId);
        
        return $this->commandOkResponse();
    }

    public function show($teamId, $memberId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        
        $service = $this->buildViewService();
        $member = $service->showById($this->firmId(), $teamId, $memberId);
        return $this->singleQueryResponse($this->arrayDataOfMember($member));
    }

    public function showAll($teamId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        $service = $this->buildViewService();
        $members = $service->showAll($this->firmId(), $teamId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($members);
        foreach ($members as $member) {
            $result["list"][] = $this->arrayDataOfMember($member);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfMember(Member $member): array
    {
        return [
            "id" => $member->getId(),
            "position" => $member->getPosition(),
            "anAdmin" => $member->isAnAdmin(),
            "active" => $member->isActive(),
            "joinTime" => $member->getJoinTimeString(),
            "client" => [
                "id" => $member->getClient()->getId(),
                "name" => $member->getClient()->getFullName(),
            ],
        ];
    }

    protected function buildViewService()
    {
        $memberRepository = $this->em->getRepository(Member::class);
        return new ViewMember($memberRepository);
    }

    protected function buildAddService()
    {
        $memberRepository = $this->em->getRepository(Member2::class);
        $clientRepository = $this->em->getRepository(Client::class);
        return new AddMember($memberRepository, $clientRepository);
    }

    protected function buildRemoveService()
    {
        $memberRepository = $this->em->getRepository(Member2::class);
        return new RemoveMember($memberRepository);
    }

}
