<?php

namespace Firm\Application\Service\Client\ProgramParticipant;

use Firm\Application\Service\Firm\Program\ConsultantRepository;
use Resources\Application\Event\Dispatcher;

class InviteConsultantToAttendMeeting
{

    /**
     *
     * @var AttendeeRepository
     */
    protected $attendeeRepository;

    /**
     *
     * @var ConsultantRepository
     */
    protected $consultantRepository;

    /**
     * 
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(AttendeeRepository $attendeeRepository, ConsultantRepository $consultantRepository,
            Dispatcher $dispatcher)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->consultantRepository = $consultantRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $firmId, string $clientId, string $meetingId, string $consultantId): void
    {
        $consultant = $this->consultantRepository->aConsultantOfId($consultantId);
        $attendee = $this->attendeeRepository
                ->anAttendeeBelongsToClientParticipantCorrespondWithMeeting($firmId, $clientId, $meetingId);
        $attendee->inviteUserToAttendMeeting($consultant);
        $this->attendeeRepository->update();
        
        $this->dispatcher->dispatch($attendee);
    }

}
