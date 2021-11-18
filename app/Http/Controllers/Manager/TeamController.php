<?php

namespace App\Http\Controllers\Manager;

use Firm\Domain\Model\Firm\Client as Client2;
use Firm\Domain\Model\Firm\Team as Team2;
use Firm\Domain\Task\InFirm\AddTeamPayload;
use Firm\Domain\Task\InFirm\AddTeamTask;
use Firm\Domain\Task\InFirm\MemberDataRequest;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Team;
use Query\Domain\Model\Firm\Team\Member;
use Query\Domain\Task\Dependency\Firm\TeamFilter;
use Query\Domain\Task\Dependency\PaginationFilter;
use Query\Domain\Task\InFirm\ShowAllTeamTask;
use Query\Domain\Task\InFirm\ShowTeamTask;

class TeamController extends ManagerBaseController
{
    public function add()
    {
        $teamRepository = $this->em->getRepository(Team2::class);
        $clientRepository = $this->em->getRepository(Client2::class);
        
        $name = $this->stripTagsInputRequest('name');
        $payload = new AddTeamPayload($name);
        foreach ($this->request['members'] as $memberRequest) {
            $clientId = $this->stripTagsVariable($memberRequest['clientId']);
            $position = $this->stripTagsVariable($memberRequest['position']);
            $memberDataRequest = new MemberDataRequest($clientId, $position);
            $payload->addMemberDataRequest($memberDataRequest);
        }
        $task = new AddTeamTask($teamRepository, $clientRepository, $payload);
        $this->executeFirmTaskExecutableByManager($task);
        
        $team = $this->getTeamFromShowTeamTaskExecution($task->addedTeamId);
        return $this->commandCreatedResponse($this->arrayDataOfTeam($team));
    }
    
    public function show($id)
    {
        return $this->arrayDataOfTeam($this->getTeamFromShowTeamTaskExecution($id));
    }
    
    public function showAll()
    {
        $teamRepository = $this->em->getRepository(Team::class);
        
        $paginationFilter = new PaginationFilter($this->getPage(), $this->getPageSize());
        $payload = TeamFilter::create($paginationFilter)
                ->setName($this->stripTagQueryRequest('name'))
                ->setMinimumActiveMemberCount($this->integerOfQueryRequest('minimumActiveMemberCount'));
        $task = new ShowAllTeamTask($teamRepository, $payload);
        $this->executeFirmQueryTask($task);
        
        $result = [];
        $result['total'] = count($task->results);
        foreach ($task->results as $team) {
            $result['list'][] = [
                'id' => $team->getId(),
                'name' => $team->getName(),
                'createdTime' => $team->getCreatedTimeString(),
                'creator' => $this->arrayDataOfCreator($team->getCreator()),
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    protected function getTeamFromShowTeamTaskExecution($id)
    {
        $teamRepository = $this->em->getRepository(Team::class);
        $task = new ShowTeamTask($teamRepository, $id);
        $this->executeFirmQueryTask($task);
        return $task->result;
    }
    
    protected function arrayDataOfTeam(Team $team): array
    {
        $members = [];
        foreach ($team->iterateActiveMember() as $member) {
            $members[] = $this->arrayDataOfMember($member);
        }
        return [
            'id' => $team->getId(),
            'name' => $team->getName(),
            'createdTime' => $team->getCreatedTimeString(),
            'creator' => $this->arrayDataOfCreator($team->getCreator()),
            'members' => $members,
        ];
    }
    
    protected function arrayDataOfMember(Member $member): array
    {
        return [
            'id' => $member->getId(),
            'position' => $member->getPosition(),
            'anAdmin' => $member->isAnAdmin(),
            'joinTime' => $member->getJoinTimeString(),
            'client' => [
                'id' => $member->getClient()->getId(),
                'name' => $member->getClient()->getFullName(),
            ],
        ];
    }
    
    protected function arrayDataOfCreator(?Client $teamCreator): ?array
    {
        return empty($teamCreator) ? null : [
            'id' => $teamCreator->getId(),
            'name' => $teamCreator->getFullName(),
        ];
    }
    
}
