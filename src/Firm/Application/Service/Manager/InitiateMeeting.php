<?php

namespace Firm\Application\Service\Manager;

use Firm\ {
    Application\Service\Firm\Program\ActivityTypeRepository,
    Application\Service\Firm\Program\MeetingType\MeetingRepository,
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
     * @var ManagerRepository
     */
    protected $managerRepository;

    /**
     *
     * @var ActivityTypeRepository
     */
    protected $activityTypeRepository;

    function __construct(
            MeetingRepository $meetingRepository, ManagerRepository $managerRepository,
            ActivityTypeRepository $activityTypeRepository)
    {
        $this->meetingRepository = $meetingRepository;
        $this->managerRepository = $managerRepository;
        $this->activityTypeRepository = $activityTypeRepository;
    }

    public function execute(
            string $firmId, string $managerId, string $activityTypeId, MeetingData $meetingData): string
    {
        $id = $this->meetingRepository->nextIdentity();
        $meetingType = $this->activityTypeRepository->ofId($activityTypeId);
        $meeting = $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->initiateMeeting($id, $meetingType, $meetingData);
        $this->meetingRepository->add($meeting);
        return $id;
    }

}
