<?php

namespace Firm\Application\Service\Personnel;

use Firm\Application\Service\Firm\Program\ParticipantRepository;

class InviteParticipantToAttendMeeting
{

    /**
     *
     * @var MeetingAttendanceRepository
     */
    protected $meetingAttendaceRepository;

    /**
     *
     * @var ParticipantRepository
     */
    protected $participantRepository;

    function __construct(
            MeetingAttendanceRepository $meetingAttendaceRepository, ParticipantRepository $participantRepository)
    {
        $this->meetingAttendaceRepository = $meetingAttendaceRepository;
        $this->participantRepository = $participantRepository;
    }

    public function execute(string $firmId, string $personnelId, string $meetingId, string $participantId): void
    {
        $participant = $this->participantRepository->ofId($participantId);
        $this->meetingAttendaceRepository
                ->aMeetingAttendanceBelongsToPersonnelCorrespondWithMeeting($firmId, $personnelId, $meetingId)
                ->inviteUserToAttendMeeting($participant);
        $this->meetingAttendaceRepository->update();
    }

}
