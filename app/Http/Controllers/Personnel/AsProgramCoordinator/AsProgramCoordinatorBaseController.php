<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use Query\Application\Auth\Firm\Program\CoordinatorAuthorization;
use Query\Application\Service\Coordinator\ExecuteTaskAsProgramCoordinator;
use Query\Domain\Model\Firm\Program\Coordinator;
use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByCoordinator;

class AsProgramCoordinatorBaseController extends PersonnelBaseController
{

    protected function authorizedUserIsProgramCoordinator($programId)
    {
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        $authZ = new CoordinatorAuthorization($coordinatorRepository);
        $authZ->execute($this->firmId(), $this->personnelId(), $programId);
    }

    protected function executeTaskInProgram(string $programId, ITaskInProgramExecutableByCoordinator $task): void
    {
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        (new ExecuteTaskAsProgramCoordinator($coordinatorRepository))
                ->execute($this->firmId(), $this->personnelId(), $programId, $task);
    }

}
