<?php

namespace Firm\Application\Service\User\MeetingAttendee;

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

    public function execute(string $userId, string $meetingId, string $attendeeId): void
    {
        $attendee = $this->attendeeRepository->ofId($attendeeId);
        $this->attendeeRepository->anAttendeeBelongsToUserParticipantCorrespondWithMeeting($userId, $meetingId)
                ->cancelInvitationTo($attendee);
        $this->attendeeRepository->update();
    }

}
