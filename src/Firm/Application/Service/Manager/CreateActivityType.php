<?php

namespace Firm\Application\Service\Manager;

use Firm\ {
    Application\Service\Firm\Program\ActivityTypeRepository,
    Application\Service\Firm\ProgramRepository,
    Domain\Service\ActivityTypeDataProvider
};

class CreateActivityType
{

    /**
     *
     * @var ActivityTypeRepository
     */
    protected $activityTypeRepository;

    /**
     *
     * @var ManagerRepository
     */
    protected $managerRepository;

    /**
     *
     * @var ProgramRepository
     */
    protected $programRepository;

    public function __construct(
            ActivityTypeRepository $activityTypeRepository, ManagerRepository $managerRepository,
            ProgramRepository $programRepository)
    {
        $this->activityTypeRepository = $activityTypeRepository;
        $this->managerRepository = $managerRepository;
        $this->programRepository = $programRepository;
    }

    public function execute(
            string $firmId, string $managerId, string $programId, ActivityTypeDataProvider $activityTypeDataProvider): string
    {
        $program = $this->programRepository->aProgramOfId($programId);
        $id = $this->activityTypeRepository->nextIdentity();
        $activityType = $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->createActivityTypeInProgram($program, $id, $activityTypeDataProvider);
        $this->activityTypeRepository->add($activityType);
        return $id;
    }

}
