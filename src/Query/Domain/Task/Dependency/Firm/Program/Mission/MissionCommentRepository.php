<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Mission;

use Query\Domain\Model\Firm\Program\Mission\MissionComment;

interface MissionCommentRepository
{

    public function allMissionCommentInProgram(string $programId, MissionCommentFilter $filter);

    public function aMissionCommentInProgram(string $programId, string $id): MissionComment;
}
