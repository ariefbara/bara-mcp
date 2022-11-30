<?php

namespace Query\Domain\Task\Dependency\Firm\Program;

interface MissionRepository
{

    public function allMissionsWithDiscussionOverviewAccessibleByPersonnelHavingMentorAuthority(
            string $personnelId, int $page, int $pageSize);
    
    public function missionListInAllProgramCoordinatedByPersonnel(string $personnelId);
}
