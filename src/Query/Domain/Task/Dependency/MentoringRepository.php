<?php

namespace Query\Domain\Task\Dependency;

use Query\Domain\Task\Personnel\MentoringListFilterForCoordinator;

interface MentoringRepository
{

    public function allMentoringsBelongsToPersonnel(
            string $personnelId, int $page, int $pageSize, MentoringFilter $filter);

    public function allMentoringsBelongsToParticipant(
            string $participantId, int $page, int $pageSize, MentoringFilter $filter);

    public function allValidMentoringsInProgram(string $programId, ExtendedMentoringFilter $filter);

    public function mentoringListInAllProgramCoordinatedByPersonnel(
            string $personnelId, MentoringListFilterForCoordinator $filter);
}
