<?php

namespace Firm\Application\Service\Personnel\ProgramCoordinator;

use Firm\ {
    Application\Service\Personnel\ActivityTypeRepository,
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
     * @var ProgramCoordinatorRepository
     */
    protected $programCoordinatorRepository;

    /**
     *
     * @var ActivityTypeRepository
     */
    protected $activityTypeRepository;

    function __construct(
            ActivityRepository $meetingRepository, ProgramCoordinatorRepository $programCoordinatorRepository,
            ActivityTypeRepository $activityTypeRepository)
    {
        $this->meetingRepository = $meetingRepository;
        $this->programCoordinatorRepository = $programCoordinatorRepository;
        $this->activityTypeRepository = $activityTypeRepository;
    }

    public function execute(
            string $firmId, string $personnelId, string $programId, string $activityTypeId, MeetingData $meetingData): string
    {
        $id = $this->meetingRepository->nextIdentity();
        $activityType = $this->activityTypeRepository->ofId($activityTypeId);
        $meeting = $this->programCoordinatorRepository
                ->aCoordinatorCorrespondWithProgram($firmId, $personnelId, $programId)
                ->initiateMeeting($id, $activityType, $meetingData);
        $this->meetingRepository->add($meeting);
        return $id;
    }

}
