<?php

namespace Firm\Application\Service\Personnel;

use Firm\Domain\Model\Firm\Program\MeetingType\MeetingData;

class UpdateMeeting
{

    /**
     *
     * @var MeetingAttendanceRepository
     */
    protected $meetingAttendaceRepository;

    function __construct(MeetingAttendanceRepository $meetingAttendaceRepository)
    {
        $this->meetingAttendaceRepository = $meetingAttendaceRepository;
    }

    public function execute(string $firmId, string $personnelId, string $meetingId, MeetingData $meetingData): void
    {
        $this->meetingAttendaceRepository
                ->aMeetingAttendanceBelongsToPersonnelCorrespondWithMeeting($firmId, $personnelId, $meetingId)
                ->updateMeeting($meetingData);
        $this->meetingAttendaceRepository->update();
    }

}
