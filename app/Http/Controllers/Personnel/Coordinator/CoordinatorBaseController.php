<?php

namespace App\Http\Controllers\Personnel\Coordinator;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use Query\Application\Service\Coordinator\ExecuteProgramTask;
use Query\Domain\Model\Firm\Program\Coordinator;
use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;

class CoordinatorBaseController extends PersonnelBaseController
{

    protected function executeProgramQueryTask(string $coordinatorId, ProgramTaskExecutableByCoordinator $task, $payload): void
    {
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        (new ExecuteProgramTask($coordinatorRepository))
                ->execute($this->firmId(), $this->personnelId(), $coordinatorId, $task, $payload);
    }

}
