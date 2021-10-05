<?php

namespace Firm\Domain\Task\Dependency\Firm;

use Firm\Domain\Model\Firm\Program\Sponsor;

interface SponsorRepository
{

    public function nextIdentity(): string;

    public function add(Sponsor $sponsor): void;

    public function ofId(string $id): Sponsor;
}
