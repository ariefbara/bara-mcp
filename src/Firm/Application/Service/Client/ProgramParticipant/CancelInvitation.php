<?php

namespace Firm\Application\Service\Client\ProgramParticipant;

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

    public function execute(string $firmId, string $clientId, string $meetingId, string $attendeeId): void
    {
        $attendee = $this->attendeeRepository->ofId($attendeeId);
        $this->attendeeRepository->anAttendeeBelongsToClientParticipantCorrespondWithMeeting($firmId, $clientId, $meetingId)
                ->cancelInvitationTo($attendee);
        $this->attendeeRepository->update();
    }

}
