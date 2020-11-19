<?php

namespace Firm\Application\Service\Personnel;

use Firm\Application\Service\Firm\Program\CoordinatorRepository;

class InviteCoordinatorToAttendMeeting
{

    /**
     *
     * @var AttendeeRepository
     */
    protected $attendeeRepository;

    /**
     *
     * @var CoordinatorRepository
     */
    protected $coordinatorRepository;

    function __construct(AttendeeRepository $attendeeRepository, CoordinatorRepository $coordinatorRepository)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->coordinatorRepository = $coordinatorRepository;
    }

    public function execute(string $firmId, string $personnelId, string $meetingId, string $coordinatorId): void
    {
        $coordinator = $this->coordinatorRepository->aCoordinatorOfId($coordinatorId);
        $this->attendeeRepository
                ->anAttendeeBelongsToPersonnelCorrespondWithMeeting($firmId, $personnelId, $meetingId)
                ->inviteUserToAttendMeeting($coordinator);
        $this->attendeeRepository->update();
    }

}
