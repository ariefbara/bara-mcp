<?php

namespace Firm\Application\Service\Personnel;

use Firm\Application\Service\Firm\Program\ConsultantRepository;

class InviteConsultantToAttendMeeting
{

    /**
     *
     * @var MeetingAttendanceRepository
     */
    protected $meetingAttendaceRepository;

    /**
     *
     * @var ConsultantRepository
     */
    protected $consultantRepository;

    function __construct(
            MeetingAttendanceRepository $meetingAttendaceRepository, ConsultantRepository $consultantRepository)
    {
        $this->meetingAttendaceRepository = $meetingAttendaceRepository;
        $this->consultantRepository = $consultantRepository;
    }

    public function execute(string $firmId, string $personnelId, string $meetingId, string $consultantId): void
    {
        $consultant = $this->consultantRepository->aConsultantOfId($consultantId);
        $this->meetingAttendaceRepository
                ->aMeetingAttendanceBelongsToPersonnelCorrespondWithMeeting($firmId, $personnelId, $meetingId)
                ->inviteUserToAttendMeeting($consultant);
        $this->meetingAttendaceRepository->update();
    }

}
