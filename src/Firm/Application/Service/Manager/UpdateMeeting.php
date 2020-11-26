<?php

namespace Firm\Application\Service\Manager;

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

    public function execute(string $firmId, string $managerId, string $meetingId, MeetingData $meetingData): void
    {
        $this->attendeeRepository
                ->anAttendeeBelongsToManagerCorrespondWithMeeting($firmId, $managerId, $meetingId)
                ->updateMeeting($meetingData);
        $this->attendeeRepository->update();
    }

}
