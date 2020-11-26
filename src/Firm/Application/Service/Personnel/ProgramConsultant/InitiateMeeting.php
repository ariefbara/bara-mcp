<?php

namespace Firm\Application\Service\Personnel\ProgramConsultant;

use Firm\ {
    Application\Service\Firm\Program\ActivityTypeRepository,
    Application\Service\Personnel\MeetingRepository,
    Domain\Model\Firm\Program\MeetingType\MeetingData
};

class InitiateMeeting
{

    /**
     *
     * @var MeetingRepository
     */
    protected $meetingRepository;

    /**
     *
     * @var ProgramConsultantRepository
     */
    protected $programConsultantRepository;

    /**
     *
     * @var ActivityTypeRepository
     */
    protected $activityTypeRepository;

    function __construct(
            MeetingRepository $meetingRepository, ProgramConsultantRepository $programConsultantRepository,
            ActivityTypeRepository $activityTypeRepository)
    {
        $this->meetingRepository = $meetingRepository;
        $this->programConsultantRepository = $programConsultantRepository;
        $this->activityTypeRepository = $activityTypeRepository;
    }

    public function execute(
            string $firmId, string $personnelId, string $programId, string $activityTypeId, MeetingData $meetingData): string
    {
        $id = $this->meetingRepository->nextIdentity();
        $meetingType = $this->activityTypeRepository->ofId($activityTypeId);
        $meeting = $this->programConsultantRepository
                ->aConsultantCorrespondWithProgram($firmId, $personnelId, $programId)
                ->initiateMeeting($id, $meetingType, $meetingData);
        $this->meetingRepository->add($meeting);
        return $id;
    }

}
