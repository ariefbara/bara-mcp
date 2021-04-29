<?php

namespace App\Http\Controllers\User\AsProgramParticipant;

use App\Http\Controllers\User\UserBaseController;
use Firm\Domain\Model\Firm\Program\UserParticipant;
use Query\Application\Auth\Firm\Program\UserParticipantAuthorization;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\User\UserParticipant as UserParticipant2;

class AsProgramParticipantBaseController extends UserBaseController
{
    
    protected function authorizedUserIsActiveProgramParticipant(string $firmId, string $programId): void
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        $authZ = new UserParticipantAuthorization($participantRepository);
        $authZ->execute($firmId, $programId, $this->userId());
    }
    
    protected function userParticipantFirmRepository()
    {
        return $this->em->getRepository(UserParticipant::class);
    }
    
    protected function userParticipantQueryRepository()
    {
        return $this->em->getRepository(UserParticipant2::class);
    }
    
}
