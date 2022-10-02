<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Model\Firm\Program\Registrant;
use Query\Domain\Task\Dependency\PaginationFilter;
use Query\Domain\Task\Personnel\ViewAllNewProgramApplicant;
use Query\Domain\Task\Personnel\ViewAllNewProgramApplicantPayload;

class ManageableNewApplicantController extends PersonnelBaseController
{

    public function viewAll()
    {
        $registrantRepository = $this->em->getRepository(Registrant::class);
        $task = new ViewAllNewProgramApplicant($registrantRepository);

        $paginationFilter = new PaginationFilter($this->getPage(), $this->getPageSize());
        $payload = new ViewAllNewProgramApplicantPayload($paginationFilter);

        $this->executePersonalQueryTask($task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }

}
