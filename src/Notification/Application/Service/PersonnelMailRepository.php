<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Personnel\PersonnelMail;

interface PersonnelMailRepository
{

    public function nextIdentity(): string;

    public function add(PersonnelMail $personnelMail): void;
}
