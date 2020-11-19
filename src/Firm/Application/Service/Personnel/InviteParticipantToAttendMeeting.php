<?php

namespace Firm\Application\Service\Personnel;

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

    public function execute(string $firmId, string $personnelId, string $meetingId, string $participantId): void
    {
        $participant = $this->participantRepository->ofId($participantId);
        $this->attendeeRepository
                ->anAttendeeBelongsToPersonnelCorrespondWithMeeting($firmId, $personnelId, $meetingId)
                ->inviteUserToAttendMeeting($participant);
        $this->attendeeRepository->update();
    }

}
