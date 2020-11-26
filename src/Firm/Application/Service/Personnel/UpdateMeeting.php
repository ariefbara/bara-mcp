<?php

namespace Firm\Application\Service\Personnel;

use Firm\Domain\Model\Firm\Program\MeetingType\MeetingData;

class UpdateMeeting
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

    public function execute(string $firmId, string $personnelId, string $meetingId, MeetingData $meetingData): void
    {
        $this->attendeeRepository
                ->anAttendeeBelongsToPersonnelCorrespondWithMeeting($firmId, $personnelId, $meetingId)
                ->updateMeeting($meetingData);
        $this->attendeeRepository->update();
    }

}
