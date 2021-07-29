<?php

namespace App\Http\Controllers\Personnel\AsCoordinatorMeetingInitiator;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use Query\Application\Auth\AuthorizePersonnelIsActiveMeetingInitiator;
use Query\Domain\Model\Firm\Program\Activity\Invitee;

class AsMeetingInitiatorBaseController extends PersonnelBaseController
{
    protected function authorizePersonnelIsMeetingInitiator(string $meetingId)
    {
        $meetingAttendeeRepository = $this->em->getRepository(Invitee::class);
        $authZ = new AuthorizePersonnelIsActiveMeetingInitiator($meetingAttendeeRepository);
        $authZ->execute($this->firmId(), $this->personnelId(), $meetingId);
    }
}
