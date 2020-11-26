<?php

namespace Firm\Application\Service\Client\ProgramParticipant;   

use Firm\Application\Service\Firm\Program\CoordinatorRepository;

class InviteCoordinatorToAttendMeeting
{

    /**
     *
     * @var AttendeeRepository
     */
    protected $attendeeRepository;

    /**
     *
     * @var CoordinatorRepository
     */
    protected $coordinatorRepository;

    function __construct(AttendeeRepository $attendeeRepository, CoordinatorRepository $coordinatorRepository)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->coordinatorRepository = $coordinatorRepository;
    }

    public function execute(string $firmId, string $clientId, string $meetingId, string $coordinatorId): void
    {
        $coordinator = $this->coordinatorRepository->aCoordinatorOfId($coordinatorId);
        $this->attendeeRepository
                ->anAttendeeBelongsToClientParticipantCorrespondWithMeeting($firmId, $clientId, $meetingId)
                ->inviteUserToAttendMeeting($coordinator);
        $this->attendeeRepository->update();
    }

}
