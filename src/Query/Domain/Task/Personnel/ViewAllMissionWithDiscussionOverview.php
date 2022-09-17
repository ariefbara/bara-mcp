<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Model\Firm\TaskExecutableByPersonnel;
use Query\Domain\Task\Dependency\Firm\Program\MissionRepository;

class ViewAllMissionWithDiscussionOverview implements TaskExecutableByPersonnel
{

    /**
     * 
     * @var MissionRepository
     */
    protected $missionRepository;

    /**
     * 
     * @var ViewAllMissionWithDiscussionOverviewPayload
     */
    protected $payload;

    public function __construct(
            MissionRepository $missionRepository, ViewAllMissionWithDiscussionOverviewPayload $payload)
    {
        $this->missionRepository = $missionRepository;
        $this->payload = $payload;
    }

    public function execute(string $personnelId): void
    {
        $this->payload->result = $this->missionRepository->allMissionsWithDiscussionOverviewAccessibleByPersonnelHavingMentorAuthority(
                $personnelId, $this->payload->getPage(), $this->payload->getPageSize());
    }

}
