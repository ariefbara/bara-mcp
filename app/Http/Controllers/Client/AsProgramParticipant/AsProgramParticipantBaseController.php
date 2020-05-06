<?php

namespace App\Http\Controllers\Client\AsProgramParticipant;

use App\Http\Controllers\Client\ClientBaseController;
use Query\ {
    Application\Auth\Firm\Program\ParticipantAuthorization,
    Domain\Model\Firm\Program\Participant
};

class AsProgramParticipantBaseController extends ClientBaseController
{

    protected function authorizedClientIsActiveProgramParticipant(string $firmId, string $programId): void
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        $authZ = new ParticipantAuthorization($participantRepository);
        $authZ->execute($firmId, $programId, $this->clientId());
    }

}
