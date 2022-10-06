<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Model\Firm\Program;
use Query\Domain\Task\GenericQueryPayload;
use Query\Domain\Task\Personnel\ViewCoordinatedProgramsSummary;

class CoordinatedProgramsSummaryController extends PersonnelBaseController
{
    public function view()
    {
        $programRepository = $this->em->getRepository(Program::class);
        $task = new ViewCoordinatedProgramsSummary($programRepository);
        $payload = new GenericQueryPayload();
        $this->executePersonalQueryTask($task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }
}
