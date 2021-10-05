<?php

namespace Query\Domain\Task\Dependency\Firm\Program;

use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\Sponsor;

interface SponsorRepository
{

    public function allSponsorsInProgram(Program $program, int $page, int $pageSize, ?bool $activeStatus);

    public function aSponsorInProgram(Program $program, string $sponsorId): Sponsor;
}
