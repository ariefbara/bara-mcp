<?php

namespace App\Http\Controllers\Client;

use Query\Application\Service\Client\ExecuteTask;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Task\Client\ShowAllActiveIndividualAndTeamProgramParticipationTask;

class ActiveIndividualAndTeamProgramParticipationController extends ClientBaseController
{
    public function showAll()
    {
        $service = new ExecuteTask($this->clientQueryRepository());
        
        $participantRepository = $this->em->getRepository(Participant::class);
        $task = new ShowAllActiveIndividualAndTeamProgramParticipationTask($participantRepository);
        $service->execute($this->firmId(), $this->clientId(), $task);
        
        $result = [];
        foreach ($task->result as $participant) {
            $result['list'][] = $this->arrayDataOfParticipant($participant);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfParticipant(Participant $participant): array
    {
        return [
            'id' => $participant->getId(),
            'active' => $participant->isActive(),
            'enrolledTime' => $participant->getEnrolledTimeString(),
            'program' => [
                'id' => $participant->getProgram()->getId(),
                'name' => $participant->getProgram()->getName(),
                'description' => $participant->getProgram()->getDescription(),
                'strictMissionOrder' => $participant->getProgram()->isStrictMissionOrder(),
                'published' => $participant->getProgram()->isPublished(),
                'removed' => $participant->getProgram()->isRemoved(),
            ],
            'team' => $this->arrayDataOfTeamParticipant($participant->getTeamParticipant()),
        ];
    }
    protected function arrayDataOfTeamParticipant(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant) ? null : [
            'id' => $teamParticipant->getTeam()->getId(),
            'name' => $teamParticipant->getTeam()->getName(),
        ];
    }
}
