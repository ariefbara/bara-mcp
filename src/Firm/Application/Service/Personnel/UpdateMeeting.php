<?php

namespace Firm\Application\Service\Personnel;

use Firm\Domain\Model\Firm\Program\MeetingType\MeetingData;
use Resources\Application\Event\Dispatcher;

class UpdateMeeting
{

    /**
     *
     * @var AttendeeRepository
     */
    protected $attendeeRepository;

    /**
     * 
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(AttendeeRepository $attendeeRepository, Dispatcher $dispatcher)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $firmId, string $personnelId, string $meetingId, MeetingData $meetingData): void
    {
        $attendee = $this->attendeeRepository
                ->anAttendeeBelongsToPersonnelCorrespondWithMeeting($firmId, $personnelId, $meetingId);
        $attendee->updateMeeting($meetingData);
        $this->attendeeRepository->update();
        
        $this->dispatcher->dispatch($attendee);
    }

}
