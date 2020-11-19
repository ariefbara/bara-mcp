<?php

namespace Firm\Application\Service\Personnel;

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

    public function execute(string $firmId, string $personnelId, string $meetingId, string $consultantId): void
    {
        $consultant = $this->consultantRepository->aConsultantOfId($consultantId);
        $this->attendeeRepository
                ->anAttendeeBelongsToPersonnelCorrespondWithMeeting($firmId, $personnelId, $meetingId)
                ->inviteUserToAttendMeeting($consultant);
        $this->attendeeRepository->update();
    }

}
