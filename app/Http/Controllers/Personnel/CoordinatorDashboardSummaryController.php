<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Model\Firm\Personnel;
use Query\Domain\Task\GenericQueryPayload;
use Query\Domain\Task\Personnel\ViewCoordinatorDashboardSummary;

class CoordinatorDashboardSummaryController extends PersonnelBaseController
{

    public function view()
    {
        $personnelRepository = $this->em->getRepository(Personnel::class);
        $task = new ViewCoordinatorDashboardSummary($personnelRepository);
        
        $payload = new GenericQueryPayload();
        $this->executePersonalQueryTask($task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }

}
