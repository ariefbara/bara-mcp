<?php

namespace Firm\Application\Service\User\MeetingAttendee;

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

    public function execute(string $userId, string $meetingId, MeetingData $meetingData): void
    {
        $this->attendeeRepository
                ->anAttendeeBelongsToUserParticipantCorrespondWithMeeting($userId, $meetingId)
                ->updateMeeting($meetingData);
        $this->attendeeRepository->update();
    }

}
