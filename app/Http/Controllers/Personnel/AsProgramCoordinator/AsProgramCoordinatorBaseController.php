<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use Query\ {
    Application\Auth\Firm\Program\CoordinatorAuthorization,
    Domain\Model\Firm\Program\Coordinator
};

class AsProgramCoordinatorBaseController extends PersonnelBaseController
{
    protected function authorizedUserIsProgramCoordinator($programId)
    {
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        $authZ = new CoordinatorAuthorization($coordinatorRepository);
        $authZ->execute($this->firmId(), $this->personnelId(), $programId);
    }
}
