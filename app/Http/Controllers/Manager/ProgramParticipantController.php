<?php

namespace App\Http\Controllers\Manager;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Team as Team2;
use Firm\Domain\Task\InFirm\AddClientParticipantPayload;
use Firm\Domain\Task\InFirm\AddClientParticipantTask;
use Firm\Domain\Task\InFirm\AddTeamParticipantPayload;
use Firm\Domain\Task\InFirm\AddTeamAsActiveProgramParticipant;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Team;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\Task\Dependency\Firm\Program\ParticipantFilter;
use Query\Domain\Task\InFirm\ShowAllProgramParticipantsTask;
use Query\Domain\Task\InFirm\ShowProgramParticipantTask;

class ProgramParticipantController extends ManagerBaseController
{
    public function addClientParticipant($programId)
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $programRepository = $this->em->getRepository(Program::class);
        
        $clientId = $this->stripTagsInputRequest('clientId');
        $payload = new AddClientParticipantPayload($clientId, $programId);
        
        $task = new AddClientParticipantTask($clientRepository, $programRepository, $payload);
        $this->executeFirmTaskExecutableByManager($task);
        
        return $this->show($task->addedClientParticipantId);
    }
    
    public function addTeamParticipant($programId)
    {
        $teamRepository = $this->em->getRepository(Team2::class);
        $programRepository = $this->em->getRepository(Program::class);
        
        $teamId = $this->stripTagsInputRequest('teamId');
        $payload = new AddTeamParticipantPayload($teamId, $programId);
        
        $task = new AddTeamAsActiveProgramParticipant($teamRepository, $programRepository, $payload);
        $this->executeFirmTaskExecutableByManager($task);
        
        return $this->show($task->addedTeamParticipantId);
    }
    
    public function showAll()
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        
        $filter = (new ParticipantFilter($this->getPage(), $this->getPageSize()))
                ->setActiveStatus($this->filterBooleanOfQueryRequest('activeStatus'));
        
        $programIdListRequest = $this->request->query('programIdList');
        if (isset($programIdListRequest)) {
            $programIdList = [];
            foreach ($programIdListRequest as $programId) {
                $programIdList[] = $programId;
            }
            $filter->setProgramIdList($programIdList);
        }
        
        $task = new ShowAllProgramParticipantsTask($participantRepository, $filter);
        $this->executeFirmQueryTask($task);
        
        $result = [];
        $result['total'] = count($task->results);
        foreach ($task->results as $participant) {
            $result['list'][] = $this->arrayDataOfParticipant($participant);
        }
        return $this->listQueryResponse($result);
    }
    
    public function show($id)
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        $task = new ShowProgramParticipantTask($participantRepository, $id);
        $this->executeFirmQueryTask($task);
        
        return $this->singleQueryResponse($this->arrayDataOfParticipant($task->result));
    }
    
    protected function arrayDataOfParticipant(Participant $participant): array
    {
        return [
            "id" => $participant->getId(),
            "enrolledTime" => $participant->getEnrolledTimeString(),
            "active" => $participant->isActive(),
            "note" => $participant->getNote(),
            "client" => $this->arrayDataOfClient($participant->getClientParticipant()),
            "user" => $this->arrayDataOfUser($participant->getUserParticipant()),
            "team" => $this->arrayDataOfTeam($participant->getTeamParticipant()),
        ];
    }

    protected function arrayDataOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant) ? null : [
            "id" => $clientParticipant->getClient()->getId(),
            "name" => $clientParticipant->getClient()->getFullName(),
        ];
    }

    protected function arrayDataOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant) ? null : [
            "id" => $userParticipant->getUser()->getId(),
            "name" => $userParticipant->getUser()->getFullName(),
        ];
    }

    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant) ? null : [
            "id" => $teamParticipant->getTeam()->getId(),
            "name" => $teamParticipant->getTeam()->getName(),
            'members' => $this->arrayDataOfTeamMembers($teamParticipant->getTeam()),
        ];
    }
    protected function arrayDataOfTeamMembers(?Team $team): array
    {
        $members = [];
        foreach ($team->iterateActiveMember() as $member) {
            $members[] = [
                'id' => $member->getId(),
                'client' => [
                    'id' => $member->getClient()->getId(),
                    'name' => $member->getClient()->getFullName(),
                ],
            ];
        }
        return $members;
    }
}
