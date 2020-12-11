<?php

namespace App\Http\Controllers\Client\AsProgramRegistrant;

use App\Http\Controllers\Client\ClientBaseController;
use Query\Application\Auth\AuthorizeClientIsUnconcludedProgramRegistrant;
use Query\Domain\Model\Firm\Client\ClientRegistrant;

class AsProgramRegistrantBaseController extends ClientBaseController
{
    protected function authorizeClientInUnconcludedProgramRegistrant($programId): void
    {
        $clientRegistrantRepository = $this->em->getRepository(ClientRegistrant::class);
        $authZ = new AuthorizeClientIsUnconcludedProgramRegistrant($clientRegistrantRepository);
        $authZ->execute($this->firmId(), $this->clientId(), $programId);
    }
}
