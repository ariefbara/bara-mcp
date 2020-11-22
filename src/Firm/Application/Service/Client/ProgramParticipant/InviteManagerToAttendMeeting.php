<?php

namespace Firm\Application\Service\Client\ProgramParticipant;

use Firm\Application\Service\Firm\ManagerRepository;

class InviteManagerToAttendMeeting
{

    /**
     *
     * @var AttendeeRepository
     */
    protected $attendeeRepository;

    /**
     *
     * @var ManagerRepository
     */
    protected $managerRepository;

    function __construct(AttendeeRepository $attendeeRepository, ManagerRepository $managerRepository)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->managerRepository = $managerRepository;
    }

    public function execute(string $firmId, string $clientId, string $meetingId, string $toInviteManagerId): void
    {
        $manager = $this->managerRepository->aManagerOfId($toInviteManagerId);
        $this->attendeeRepository
                ->anAttendeeBelongsToClientParticipantCorrespondWithMeeting($firmId, $clientId, $meetingId)
                ->inviteUserToAttendMeeting($manager);
        $this->attendeeRepository->update();
    }

}
