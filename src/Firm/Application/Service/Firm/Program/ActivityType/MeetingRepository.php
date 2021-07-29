<?php

namespace Firm\Application\Service\Firm\Program\ActivityType;

use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;

interface MeetingRepository
{

    public function nextIdentity(): string;

    public function add(Meeting $meeting): void;
}
