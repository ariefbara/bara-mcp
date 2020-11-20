<?php

namespace Firm\Application\Service\Manager;

class CancelInvitation
{

    /**
     *
     * @var AttendeeRepository
     */
    protected $attendeeRepository;

    function __construct(AttendeeRepository $attendeeRepository)
    {
        $this->attendeeRepository = $attendeeRepository;
    }

    public function execute(string $firmId, string $managerId, string $meetingId, string $attendeeId): void
    {
        $attendee = $this->attendeeRepository->ofId($attendeeId);
        $this->attendeeRepository->anAttendeeBelongsToManagerCorrespondWithMeeting($firmId, $managerId, $meetingId)
                ->cancelInvitationTo($attendee);
        $this->attendeeRepository->update();
    }

}
