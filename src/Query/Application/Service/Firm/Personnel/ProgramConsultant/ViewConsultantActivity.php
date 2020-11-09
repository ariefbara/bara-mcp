<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant;

use Query\Domain\Model\Firm\Program\Consultant\ConsultantActivity;

class ViewConsultantActivity
{

    /**
     *
     * @var ConsultantActivityRepository
     */
    protected $consultantActivityRepository;

    function __construct(ConsultantActivityRepository $consultantActivityRepository)
    {
        $this->consultantActivityRepository = $consultantActivityRepository;
    }

    public function showAll(string $firmId, string $personnelId, string $consultantId, int $page, int $pageSize)
    {
        return $this->consultantActivityRepository->allActivitiesBelongsToConsultant($firmId, $personnelId,
                        $consultantId, $page, $pageSize);
    }

    public function showById(string $firmId, string $personnelId, string $activityId): ConsultantActivity
    {
        return $this->consultantActivityRepository->anActivityBelongsToConsultant($firmId, $personnelId, $activityId);
    }

}
