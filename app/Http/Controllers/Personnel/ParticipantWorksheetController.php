<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Model\Firm\Program\Participant\Worksheet;
use Query\Domain\Task\Personnel\ViewAllUncommentedWorksheet;
use Query\Domain\Task\Personnel\ViewAllUncommentedWorksheetPayload;

class ParticipantWorksheetController extends PersonnelBaseController
{

    public function allUncommented()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        $viewAllUncommentedWorksheetPayload = new ViewAllUncommentedWorksheetPayload(
                $this->getPage(), $this->getPageSize());
        $task = new ViewAllUncommentedWorksheet($worksheetRepository, $viewAllUncommentedWorksheetPayload);
        
        $this->executePersonnelQueryTask($task);
        
        $result = [];
        foreach ($viewAllUncommentedWorksheetPayload->result as $worksheet) {
            
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfWorksheet(Worksheet $worksheet): array
    {
        return [
            'id' => $worksheet->getId(),
        ];
    }

}
