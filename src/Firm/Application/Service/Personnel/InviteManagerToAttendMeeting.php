<?php

namespace Firm\Application\Service\Personnel;

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

    public function execute(string $firmId, string $personnelId, string $meetingId, string $managerId): void
    {
        $manager = $this->managerRepository->aManagerOfId($managerId);
        $this->attendeeRepository
                ->anAttendeeBelongsToPersonnelCorrespondWithMeeting($firmId, $personnelId, $meetingId)
                ->inviteUserToAttendMeeting($manager);
        $this->attendeeRepository->update();
    }

}
