<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\Activity\Invitee\InviteeReport;

class ViewActivityReport
{

    /**
     * 
     * @var ActivityReportRepository
     */
    protected $activityReportRepository;

    function __construct(ActivityReportRepository $activityReportRepository)
    {
        $this->activityReportRepository = $activityReportRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $programId
     * @param string $activityId
     * @param int $page
     * @param int $pageSize
     * @return InviteeReport[]
     */
    public function showAllReportInActivity(
            string $firmId, string $programId, string $activityId, int $page, int $pageSize)
    {
        return $this->activityReportRepository
                        ->allActivityReportInActivity($firmId, $programId, $activityId, $page, $pageSize);
    }

    public function showById(string $firmId, string $programId, string $activityReportId): InviteeReport
    {
        return $this->activityReportRepository->anActivityReportInProgram($firmId, $programId, $activityReportId);
    }

}
