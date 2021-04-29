<?php

namespace App\Http\Controllers\Client\AsProgramParticipant;

use App\Http\Controllers\Client\ClientBaseController;
use Firm\Domain\Model\Firm\Program\ClientParticipant;
use Query\Application\Auth\Firm\Program\ParticipantAuthorization;
use Query\Domain\Model\Firm\Client\ClientParticipant as ClientParticipant2;
use Query\Domain\Model\Firm\Program\Participant;

class AsProgramParticipantBaseController extends ClientBaseController
{

    protected function authorizedClientIsActiveProgramParticipant(string $programId): void
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        $authZ = new ParticipantAuthorization($participantRepository);
        $authZ->execute($this->firmId(), $programId, $this->clientId());
    }
    
    protected function clientParticipantFirmRepository()
    {
        return $this->em->getRepository(ClientParticipant::class);
    }
    protected function clientParticipantQueryRepository()
    {
        return $this->em->getRepository(ClientParticipant2::class);
    }

}
