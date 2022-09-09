<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Participant\Worksheet;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\Task\Personnel\ViewAllUncommentedWorksheet;
use Query\Domain\Task\Personnel\ViewAllUncommentedWorksheetPayload;

class DedicatedMenteeWorksheetController extends PersonnelBaseController
{

    public function showAllUncommented()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        $viewAllUncommentedWorksheetPayload = new ViewAllUncommentedWorksheetPayload(
                $this->getPage(), $this->getPageSize());
        $task = new ViewAllUncommentedWorksheet($worksheetRepository, $viewAllUncommentedWorksheetPayload);
        
        $this->executePersonnelQueryTask($task);
        
        $result = [];
        $result['total'] = count($viewAllUncommentedWorksheetPayload->result);
        foreach ($viewAllUncommentedWorksheetPayload->result as $worksheet) {
            $result['list'][] = $this->arrayDataOfWorksheet($worksheet);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfWorksheet(Worksheet $worksheet): array
    {
        return [
            'id' => $worksheet->getId(),
            'name' => $worksheet->getName(),
            'mission' => [
                'id' => $worksheet->getMission()->getId(),
                'name' => $worksheet->getMission()->getName(),
            ],
            'participant' => [
                'id' => $worksheet->getParticipant()->getId(),
                'client' => $this->arrayDataOfClient($worksheet->getParticipant()->getClientParticipant()),
                'team' => $this->arrayDataOfTeam($worksheet->getParticipant()->getTeamParticipant()),
                'user' => $this->arrayDataOfUser($worksheet->getParticipant()->getUserParticipant()),
                'program' => [
                    'id' => $worksheet->getParticipant()->getProgram()->getId(),
                    'name' => $worksheet->getParticipant()->getProgram()->getName(),
                ],
            ],
        ];
    }
    
    protected function arrayDataOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant) ? null : [
            'id' => $clientParticipant->getClient()->getId(),
            'name' => $clientParticipant->getClient()->getFullName(),
        ];
    }

    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant) ? null : [
            'id' => $teamParticipant->getTeam()->getId(),
            'name' => $teamParticipant->getTeam()->getName(),
        ];
    }

    protected function arrayDataOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant) ? null : [
            'id' => $userParticipant->getUser()->getId(),
            'name' => $userParticipant->getUser()->getFullName(),
        ];
    }

}
