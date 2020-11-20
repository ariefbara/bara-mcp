<?php

namespace Firm\Application\Service\Manager;

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

    public function execute(string $firmId, string $managerId, string $meetingId, string $coordinatorId): void
    {
        $coordinator = $this->coordinatorRepository->aCoordinatorOfId($coordinatorId);
        $this->attendeeRepository
                ->anAttendeeBelongsToManagerCorrespondWithMeeting($firmId, $managerId, $meetingId)
                ->inviteUserToAttendMeeting($coordinator);
        $this->attendeeRepository->update();
    }

}
