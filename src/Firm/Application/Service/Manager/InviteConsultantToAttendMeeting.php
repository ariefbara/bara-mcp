<?php

namespace Firm\Application\Service\Manager;

use Firm\Application\Service\Firm\Program\ConsultantRepository;

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

    function __construct(
            AttendeeRepository $attendeeRepository, ConsultantRepository $consultantRepository)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->consultantRepository = $consultantRepository;
    }

    public function execute(string $firmId, string $managerId, string $meetingId, string $consultantId): void
    {
        $consultant = $this->consultantRepository->aConsultantOfId($consultantId);
        $this->attendeeRepository
                ->anAttendeeBelongsToManagerCorrespondWithMeeting($firmId, $managerId, $meetingId)
                ->inviteUserToAttendMeeting($consultant);
        $this->attendeeRepository->update();
    }

}
