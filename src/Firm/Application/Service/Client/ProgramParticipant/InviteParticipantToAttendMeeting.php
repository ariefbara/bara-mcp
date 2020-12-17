<?php

namespace Firm\Application\Service\Client\ProgramParticipant;

use Firm\Application\Service\Firm\Program\ParticipantRepository;
use Resources\Application\Event\Dispatcher;

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

    /**
     * 
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(
            AttendeeRepository $attendeeRepository, ParticipantRepository $participantRepository, Dispatcher $dispatcher)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->participantRepository = $participantRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $firmId, string $clientId, string $meetingId, string $participantId): void
    {
        $participant = $this->participantRepository->ofId($participantId);
        $attendee = $this->attendeeRepository
                ->anAttendeeBelongsToClientParticipantCorrespondWithMeeting($firmId, $clientId, $meetingId);
        $attendee->inviteUserToAttendMeeting($participant);
        $this->attendeeRepository->update();
        
        $this->dispatcher->dispatch($attendee);
    }

}
