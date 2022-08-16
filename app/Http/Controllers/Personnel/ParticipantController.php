<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\Task\Dependency\Firm\Program\ParticipantFilter;
use Query\Domain\Task\Personnel\ViewAllManageableParticipant;

class ParticipantController extends PersonnelBaseController
{

    public function showAll()
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        $page = $this->getPage();
        $pageSize = $this->getPageSize();
        $participantFilter = new ParticipantFilter($page, $pageSize);
        $statusFilter = $this->request->query('status') ?? [];
        foreach ($statusFilter as $status) {
            $participantFilter->addParticipantStatusFilter($status);
        }
        $task = new ViewAllManageableParticipant($participantRepository, $participantFilter);
        $this->executePersonnelQueryTask($task);

        $result = [];
        $result['total'] = count($task->result);
        foreach ($task->result as $participant) {
            $result['list'][] = [
                'id' => $participant->getId(),
                'status' => $participant->getStatus(),
                'programPriceSnapshot' => $participant->getProgramPrice(),
                'user' => $this->arrayDataOfUser($participant->getUserParticipant()),
                'client' => $this->arrayDataOfClient($participant->getClientParticipant()),
                'team' => $this->arrayDataOfTeam($participant->getTeamParticipant()),
                'program' => [
                    'id' => $participant->getProgram()->getId(),
                    'name' => $participant->getProgram()->getName(),
                    'price' => $participant->getProgram()->getPrice(),
                ],
            ];
        }
        
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant) ? null : [
            'id' => $userParticipant->getUser()->getId(),
            'name' => $userParticipant->getUser()->getFullName(),
        ];
    }
    protected function arrayDataOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant) ? null : [
            'id' => $clientParticipant->getClient()->getId(),
            'name' => $clientParticipant->getClient()->getFullName(),
        ];
    }
    protected function arrayDataOfTeam(?\Query\Domain\Model\Firm\Team\TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant) ? null : [
            'id' => $teamParticipant->getTeam()->getId(),
            'name' => $teamParticipant->getTeam()->getName(),
        ];
    }

}
