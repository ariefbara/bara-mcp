<?php

namespace Firm\Application\Service\Client\ProgramParticipant;

use Firm\Application\Service\Firm\Program\ParticipantRepository;

class InviteParticipantToAttendMeeting
{

    /**
     *
     * @var AttendeeRepository
     */
    protected $attendeeRepository;

    /**
     *
     * @var ParticipantRepository
     */
    protected $participantRepository;

    function __construct(
            AttendeeRepository $attendeeRepository, ParticipantRepository $participantRepository)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->participantRepository = $participantRepository;
    }

    public function execute(string $firmId, string $clientId, string $meetingId, string $participantId): void
    {
        $participant = $this->participantRepository->ofId($participantId);
        $this->attendeeRepository
                ->anAttendeeBelongsToClientParticipantCorrespondWithMeeting($firmId, $clientId, $meetingId)
                ->inviteUserToAttendMeeting($participant);
        $this->attendeeRepository->update();
    }

}
