<?php

namespace Firm\Application\Service\Personnel\ProgramCoordinator;

use Firm\Application\Service\Personnel\ActivityTypeRepository;
use Firm\Application\Service\Personnel\MeetingRepository;
use Firm\Domain\Model\Firm\Program\MeetingType\MeetingData;
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
     * @var ProgramCoordinatorRepository
     */
    protected $programCoordinatorRepository;

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

    function __construct(
            MeetingRepository $meetingRepository, ProgramCoordinatorRepository $programCoordinatorRepository,
            ActivityTypeRepository $activityTypeRepository, Dispatcher $dispatcher)
    {
        $this->meetingRepository = $meetingRepository;
        $this->programCoordinatorRepository = $programCoordinatorRepository;
        $this->activityTypeRepository = $activityTypeRepository;
        $this->dispatcher = $dispatcher;
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
        
        $this->dispatcher->dispatch($meeting);
        return $id;
    }

}
