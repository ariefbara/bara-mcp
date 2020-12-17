<?php

namespace Firm\Application\Service\Client\ProgramParticipant;

use Resources\Application\Event\Dispatcher;

class CancelInvitation
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

    public function execute(string $firmId, string $clientId, string $meetingId, string $attendeeId): void
    {
        $attendee = $this->attendeeRepository->ofId($attendeeId);
        $this->attendeeRepository
                ->anAttendeeBelongsToClientParticipantCorrespondWithMeeting($firmId, $clientId,$meetingId)
                ->cancelInvitationTo($attendee);
        $this->attendeeRepository->update();
        
        $this->dispatcher->dispatch($attendee);
    }

}
