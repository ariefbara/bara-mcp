<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Model\Firm\Personnel;
use Query\Domain\Task\Personnel\ViewMentorDashboard;
use Query\Domain\Task\Personnel\ViewMentorDashboardPayload;

class MentorDashboardController extends PersonnelBaseController
{
    public function view()
    {
        $personnelRepository = $this->em->getRepository(Personnel::class);
        $payload = new ViewMentorDashboardPayload();
        $task = new ViewMentorDashboard($personnelRepository, $payload);
        $this->executePersonnelQueryTask($task);
        
        return $this->listQueryResponse($payload->result);
    }
}
