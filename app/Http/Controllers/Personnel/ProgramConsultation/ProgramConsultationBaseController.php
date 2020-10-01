<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use Query\Domain\Model\Firm\Program\Consultant;

class ProgramConsultationBaseController extends PersonnelBaseController
{
    protected function programConsultationRepository()
    {
        return $this->em->getRepository(Consultant::class);
    }
}
