<?php

namespace Firm\Application\Service\Manager;

use Firm\Application\Service\Firm\Program\ActivityType\MeetingRepository;
use Firm\Application\Service\Firm\Program\ActivityTypeRepository;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Resources\Application\Event\Dispatcher;

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

    /**
     * 
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(MeetingRepository $meetingRepository, ManagerRepository $managerRepository,
            ActivityTypeRepository $activityTypeRepository, Dispatcher $dispatcher)
    {
        $this->meetingRepository = $meetingRepository;
        $this->managerRepository = $managerRepository;
        $this->activityTypeRepository = $activityTypeRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $managerId, string $activityTypeId, MeetingData $meetingData): string
    {
        $id = $this->meetingRepository->nextIdentity();
        $meetingType = $this->activityTypeRepository->ofId($activityTypeId);
        $meeting = $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->initiateMeeting($id, $meetingType, $meetingData);
        $this->meetingRepository->add($meeting);
        
        $this->dispatcher->dispatch($meeting);
        return $id;
    }

}
