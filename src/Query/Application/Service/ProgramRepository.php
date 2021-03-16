<?php

namespace Query\Application\Service;

use Query\Domain\Model\Firm\Program;

interface ProgramRepository
{

    public function aPublishedProgram(string $id): Program;

    public function allPublishedProgram(int $page, int $pageSize);
}
