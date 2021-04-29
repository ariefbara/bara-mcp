<?php

namespace Query\Domain\Service\Firm\Program\Mission;

use Query\Domain\Model\Firm\Program\Mission\MissionComment;

interface MissionCommentRepository
{

    public function aMissionCommentInProgram(string $programId, string $missionCommentId): MissionComment;

    public function allMissionCommentsBelongsInMission(string $programId, string $missionId, int $page, int $pageSize);
}
