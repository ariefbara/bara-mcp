<?php

namespace App\Http\Controllers\User\ProgramParticipation\AsMeetingInitiator;

use App\Http\Controllers\User\UserBaseController;
use Query\ {
    Application\Auth\AuthorizeUserIsActiveMeetingInitiator,
    Domain\Model\Firm\Program\Activity\Invitee
};

class AsMeetingInitiatorBaseController extends UserBaseController
{

    protected function authorizeUserIsMeetingInitiator($meetingId)
    {
        $meetingAttendeeRepository = $this->em->getRepository(Invitee::class);
        $authZ = new AuthorizeUserIsActiveMeetingInitiator($meetingAttendeeRepository);
        $authZ->execute($this->userId(), $meetingId);
    }

}
