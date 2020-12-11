<?php

namespace App\Http\Controllers\User\AsProgramRegistrant;

use App\Http\Controllers\User\UserBaseController;
use Query\Application\Auth\AuthorizeUserIsUnconcludedProgramRegistrant;
use Query\Domain\Model\User\UserRegistrant;

class AsProgramRegistrantBaseController extends UserBaseController
{

    protected function authorizeUserIsUnconcludedProgramRegistrant($firmId, $programId): void
    {
        $userRegistrantRepository = $this->em->getRepository(UserRegistrant::class);
        $authZ = new AuthorizeUserIsUnconcludedProgramRegistrant($userRegistrantRepository);
        $authZ->execute($this->userId(), $firmId, $programId);
    }

}
