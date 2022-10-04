<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Model\Firm\Program\Participant\MentoringRequest;
use Query\Domain\Task\Dependency\PaginationFilter;
use Query\Domain\Task\Personnel\ViewAllUnconcludedMentoringRequestInManageableProgram;
use Query\Domain\Task\Personnel\ViewAllUnconcludedMentoringRequestInManageableProgramPayload;

class UnconcludedMentoringRequestInManageableProgramController extends PersonnelBaseController
{
    public function viewAll()
    {
        $mentoringRequestRepository = $this->em->getRepository(MentoringRequest::class);
        $task = new ViewAllUnconcludedMentoringRequestInManageableProgram($mentoringRequestRepository);
        
        $paginationFilter = new PaginationFilter($this->getPage(), $this->getPageSize());
        $payload = new ViewAllUnconcludedMentoringRequestInManageableProgramPayload($paginationFilter);
        
        $this->executePersonalQueryTask($task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }
}
