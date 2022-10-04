<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Model\Firm\Program\Participant\Worksheet;
use Query\Domain\Task\Dependency\PaginationFilter;
use Query\Domain\Task\Personnel\ViewUncommentedWorksheetListInCoordinatedPrograms;
use Query\Domain\Task\Personnel\ViewUncommentedWorksheetListInCoordinatedProgramsPayload;

class UncommentedWorksheetListInCoordinatedProgramsController extends PersonnelBaseController
{
    public function viewAll()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        $task = new ViewUncommentedWorksheetListInCoordinatedPrograms($worksheetRepository);
        
        $paginationFilter = new PaginationFilter($this->getPage(), $this->getPageSize());
        $payload = new ViewUncommentedWorksheetListInCoordinatedProgramsPayload($paginationFilter);
        
        $this->executePersonalQueryTask($task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }
}
