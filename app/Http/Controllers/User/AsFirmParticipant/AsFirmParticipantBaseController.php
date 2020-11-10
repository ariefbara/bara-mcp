<?php

namespace App\Http\Controllers\User\AsFirmParticipant;

use App\Http\Controllers\User\UserBaseController;
use Query\ {
    Application\Auth\Firm\AuthorizeUserIsActiveParticipantInFirm,
    Domain\Model\Firm\Program\Participant
};

class AsFirmParticipantBaseController extends UserBaseController
{

    protected function authorizeUserIsActiveParticipantInFirm(string $firmId): void
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        $authZ = new AuthorizeUserIsActiveParticipantInFirm($participantRepository);
        $authZ->execute($firmId, $this->userId());
    }

}
