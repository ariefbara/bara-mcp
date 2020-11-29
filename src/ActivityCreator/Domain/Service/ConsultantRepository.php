<?php

namespace ActivityCreator\Domain\service;

use Query\Domain\Model\Firm\Program\Consultant;

interface ConsultantRepository
{

    public function ofId(string $consultantId): Consultant;
}
