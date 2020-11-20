<?php

namespace Firm\Application\Service\Manager;

use Firm\Application\Service\Firm\ManagerRepository;

class InviteManagerToAttendMeeting
{

    /**
     *
     * @var AttendeeRepository
     */
    protected $attendeeRepository;

    /**
     *
     * @var ManagerRepository
     */
    protected $managerRepository;

    function __construct(AttendeeRepository $attendeeRepository, ManagerRepository $managerRepository)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->managerRepository = $managerRepository;
    }

    public function execute(string $firmId, string $managerId, string $meetingId, string $toInviteManagerId): void
    {
        $manager = $this->managerRepository->aManagerOfId($toInviteManagerId);
        $this->attendeeRepository
                ->anAttendeeBelongsToManagerCorrespondWithMeeting($firmId, $managerId, $meetingId)
                ->inviteUserToAttendMeeting($manager);
        $this->attendeeRepository->update();
    }

}
