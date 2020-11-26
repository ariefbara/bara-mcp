<?php

namespace Firm\Application\Service\User\MeetingAttendee;

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

    public function execute(string $userId, string $meetingId, string $coordinatorId): void
    {
        $coordinator = $this->coordinatorRepository->aCoordinatorOfId($coordinatorId);
        $this->attendeeRepository
                ->anAttendeeBelongsToUserParticipantCorrespondWithMeeting($userId, $meetingId)
                ->inviteUserToAttendMeeting($coordinator);
        $this->attendeeRepository->update();
    }

}
