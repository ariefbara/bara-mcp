<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Personnel;

interface PersonnelRepository
{

    public function ofId(string $personnelId): Personnel;
}
