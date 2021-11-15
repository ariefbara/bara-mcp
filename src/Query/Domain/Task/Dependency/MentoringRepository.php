<?php

namespace Query\Domain\Task\Dependency;

interface MentoringRepository
{
    public function allMentoringsBelongsToPersonnel(
            string $personnelId, int $page, int $pageSize, MentoringFilter $filter);
}
