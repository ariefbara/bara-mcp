<?php

namespace Firm\Application\Service\Personnel;

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

    public function execute(string $firmId, string $personnelId, string $meetingId, string $participantId): void
    {
        $participant = $this->participantRepository->ofId($participantId);
        $attendee = $this->attendeeRepository
                ->anAttendeeBelongsToPersonnelCorrespondWithMeeting($firmId, $personnelId, $meetingId);
        $attendee->inviteUserToAttendMeeting($participant);
        $this->attendeeRepository->update();
        
        $this->dispatcher->dispatch($attendee);
    }

}
