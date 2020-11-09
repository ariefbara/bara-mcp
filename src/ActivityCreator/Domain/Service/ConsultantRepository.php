<?php

namespace ActivityCreator\Domain\service;

use ActivityCreator\Domain\DependencyModel\Firm\Personnel\Consultant;


interface ConsultantRepository
{
    public function ofId(string $consultantId): Consultant;
}
