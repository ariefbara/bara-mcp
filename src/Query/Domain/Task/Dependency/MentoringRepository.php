<?php

namespace Query\Domain\Task\Dependency;

interface MentoringRepository
{

    public function allMentoringsBelongsToPersonnel(
            string $personnelId, int $page, int $pageSize, MentoringFilter $filter);

    public function allMentoringsBelongsToParticipant(
            string $participantId, int $page, int $pageSize, MentoringFilter $filter);
}
