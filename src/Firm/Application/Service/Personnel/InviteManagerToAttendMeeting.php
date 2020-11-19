<?php

namespace Firm\Application\Service\Personnel;

use Firm\Application\Service\Firm\ManagerRepository;

class InviteManagerToAttendMeeting
{

    /**
     *
     * @var MeetingAttendanceRepository
     */
    protected $meetingAttendaceRepository;

    /**
     *
     * @var ManagerRepository
     */
    protected $managerRepository;

    function __construct(MeetingAttendanceRepository $meetingAttendaceRepository, ManagerRepository $managerRepository)
    {
        $this->meetingAttendaceRepository = $meetingAttendaceRepository;
        $this->managerRepository = $managerRepository;
    }

    public function execute(string $firmId, string $personnelId, string $meetingId, string $managerId): void
    {
        $manager = $this->managerRepository->aManagerOfId($managerId);
        $this->meetingAttendaceRepository
                ->aMeetingAttendanceBelongsToPersonnelCorrespondWithMeeting($firmId, $personnelId, $meetingId)
                ->inviteUserToAttendMeeting($manager);
        $this->meetingAttendaceRepository->update();
    }

}
