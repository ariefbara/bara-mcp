<?php

namespace App\Http\Controllers\User\AsProgramParticipant;

use App\Http\Controllers\User\UserBaseController;
use Query\ {
    Application\Auth\Firm\Program\UserParticipantAuthorization,
    Domain\Model\Firm\Program\Participant
};

class AsProgramParticipantBaseController extends UserBaseController
{
    
    protected function authorizedUserIsActiveProgramParticipant(string $firmId, string $programId)
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        $authZ = new UserParticipantAuthorization($participantRepository);
        $authZ->execute($firmId, $programId, $this->userId());
    }
}
