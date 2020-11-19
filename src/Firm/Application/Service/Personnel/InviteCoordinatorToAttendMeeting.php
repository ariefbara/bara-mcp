<?php

namespace Firm\Application\Service\Personnel;

use Firm\Application\Service\Firm\Program\CoordinatorRepository;

class InviteCoordinatorToAttendMeeting
{

    /**
     *
     * @var MeetingAttendanceRepository
     */
    protected $meetingAttendaceRepository;

    /**
     *
     * @var CoordinatorRepository
     */
    protected $coordinatorRepository;

    function __construct(MeetingAttendanceRepository $meetingAttendaceRepository, CoordinatorRepository $coordinatorRepository)
    {
        $this->meetingAttendaceRepository = $meetingAttendaceRepository;
        $this->coordinatorRepository = $coordinatorRepository;
    }

    public function execute(string $firmId, string $personnelId, string $meetingId, string $coordinatorId): void
    {
        $coordinator = $this->coordinatorRepository->aCoordinatorOfId($coordinatorId);
        $this->meetingAttendaceRepository
                ->aMeetingAttendanceBelongsToPersonnelCorrespondWithMeeting($firmId, $personnelId, $meetingId)
                ->inviteUserToAttendMeeting($coordinator);
        $this->meetingAttendaceRepository->update();
    }

}
