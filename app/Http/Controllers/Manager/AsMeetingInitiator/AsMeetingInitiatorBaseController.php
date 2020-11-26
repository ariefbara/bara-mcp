<?php

namespace App\Http\Controllers\Manager\AsMeetingInitiator;

use App\Http\Controllers\Manager\ManagerBaseController;
use Query\ {
    Application\Auth\AuthorizeManagerIsActiveMeetingInitiator,
    Domain\Model\Firm\Program\Activity\Invitee
};

class AsMeetingInitiatorBaseController extends ManagerBaseController
{
    protected function authorizeManagerIsMeetingInitiator($meetingId)
    {
        $meetingAttendeeRepository = $this->em->getRepository(Invitee::class);
        $authZ = new AuthorizeManagerIsActiveMeetingInitiator($meetingAttendeeRepository);
        $authZ->execute($this->firmId(), $this->managerId(), $meetingId);
    }
}
